<?php

function exlog_get_external_db_instance_and_fields($dbType) {
	try {
		$host = exlog_get_option("external_login_option_db_host");
		$port = exlog_get_option("external_login_option_db_port");
		$user = exlog_get_option("external_login_option_db_username");
		$password = exlog_get_option("external_login_option_db_password");
		$dbname = exlog_get_option("external_login_option_db_name");

		$db_instance = null;
		$mySqlHost = null;
		if ($dbType == "mssql") {
			$connectionOptions = array(
                "Database" => exlog_get_option("external_login_option_db_name"),
                "UID" => exlog_get_option("external_login_option_db_username"),
                "PWD" => exlog_get_option("external_login_option_db_password"),
                "APP" => "WordPressExternalLogin",
                "ApplicationIntent" => "ReadOnly"
            );
			$db_instance = sqlsrv_connect( exlog_get_option("external_login_option_db_host"), $connectionOptions);
			if( $db_instance === false ) {
				error_log('EXLOG:');
				error_log(FormatErrors(sqlsrv_errors()));
				return false;
			}
		}
		else if ($dbType == "postgresql") {
			$postgresConnectionString = "";
			if ($host) {
				$postgresConnectionString .= " host=" . $host;
			}

			if ($port) {
				$postgresConnectionString .= " port=" . $port;
			}

			if ($user) {
				$postgresConnectionString .= " user=" . $user;
			}

			if ($password) {
				$postgresConnectionString .= " password=" . $password;
			}

			if ($dbname) {
				$postgresConnectionString .= " dbname=" . $dbname;
			}

			$db_instance = pg_connect($postgresConnectionString) or error_log("EXLOG: Cannot connect to the external database.");
		} else {
			$mySqlHost = $host;

			$hostIncludesPort = strpos($host, ':') !== false;

			if ($port && !$hostIncludesPort) { //If port is included in $host, don't add the $port variable as well
				$mySqlHost .= ":" . $port;
			}

			$db_instance = new wpdb(
				$user,
				$password,
				$dbname,
				$mySqlHost
			);
		}

		$data = array(
			"db_instance" => $db_instance,
			"dbstructure_table" => exlog_get_option('exlog_dbstructure_table'),
			"dbstructure_username" => exlog_get_option('exlog_dbstructure_username'),
			"dbstructure_password" => exlog_get_option('exlog_dbstructure_password'),
			"dbstructure_first_name" => exlog_get_option('exlog_dbstructure_first_name'),
			"dbstructure_last_name" => exlog_get_option('exlog_dbstructure_last_name'),
			"dbstructure_role" => exlog_get_option('exlog_dbstructure_role'),
			"dbstructure_email" => exlog_get_option('exlog_dbstructure_email'),
		);

		if (exlog_get_option('external_login_option_db_salting_method') == 'all') {
			$data['dbstructure_salt'] = exlog_get_option('exlog_dbstructure_salt');
		}

		return $data;
	}
	catch (Exception $ex) {
		error_log('EXLOG: Unable to create database connection:');
		error_log(var_export($ex, true));
		return false;
	}
};

function exlog_build_wp_user_data($db_data, $userData) {
    return array(
        "username" => $userData[$db_data["dbstructure_username"]],
        "password" => $userData[$db_data["dbstructure_password"]],
        "first_name" => $userData[$db_data["dbstructure_first_name"]],
        "last_name" => $userData[$db_data["dbstructure_last_name"]],
        "role" => $userData[$db_data["dbstructure_role"]],
        "email" => $userData[$db_data["dbstructure_email"]],
    );
}

function exlog_auth_query($username, $password) {
	try {
		$dbType = exlog_get_option('external_login_option_db_type');

		$db_data = exlog_get_external_db_instance_and_fields($dbType);

		if ($db_data == false) {
            return false;
        }

		$userData = null;

		$exclude_query_string_component = "";
		if (exlog_get_option('external_login_option_enable_exclude_users') == "on") {
			$exclude_query_string_component = exlog_build_exclude_query_string_component($db_data);
		}

		if ($dbType == "mssql") {
			$query_string =
			'SELECT *' .
			' FROM ' . esc_sql($db_data["dbstructure_table"]) .
			' WHERE ' . esc_sql($db_data["dbstructure_username"]) . '=\'' . esc_sql($username) . '\'';
			
			$stmt = sqlsrv_query($db_data["db_instance"], $query_string);
			if (sqlsrv_has_rows($stmt) != true) {
				return array("valid" => false);
			}
			
			while( $userData = sqlsrv_fetch_array($stmt)) {
				$user_specific_salt = false;

				if (exlog_get_option('external_login_option_db_salting_method') == 'all') {
					$user_specific_salt = $userData[$db_data["dbstructure_salt"]];
				}

				$valid_credentials = exlog_validate_password($password, $userData[$db_data["dbstructure_password"]], $user_specific_salt);
				
				if ($valid_credentials) {
					$wp_user_data = exlog_build_wp_user_data($db_data, $userData);
					$wp_user_data["exlog_authenticated"] = true;
					return $wp_user_data;
				}
			}
			return array("valid" => false);
		}
		else if ($dbType == "postgresql") {
			$query_string =
				'SELECT *' .
				' FROM "' . esc_sql($db_data["dbstructure_table"]) . '"' .
				' WHERE "' . esc_sql($db_data["dbstructure_username"]) . '" ILIKE \'' . esc_sql($username) . '\'' .
			 $exclude_query_string_component;

			$rows = pg_query($query_string) or error_log("EXLOG: External DB query failed.");

			$userData = pg_fetch_array($rows, null, PGSQL_ASSOC); //Gets the first row

			pg_close($db_data["db_instance"]);

		} else {
			$query_string =
				'SELECT *' .
				' FROM ' . esc_sql($db_data["dbstructure_table"]) .
				' WHERE (' . esc_sql($db_data["dbstructure_username"]) . '="' . esc_sql($username) . '"';

			if ($db_data["dbstructure_email"]) {
				$query_string .= ' OR ' . esc_sql($db_data["dbstructure_email"]) . '="' . esc_sql($username) . '")';
			} else {
                $query_string .= ')';
            }

			$query_string .= $exclude_query_string_component;

			$rows = $db_data["db_instance"]->get_results($query_string, ARRAY_A);

			if (sizeof($rows) > 0) {
				$userData = $rows[0];
			}
		}

		if ($userData) {
			$user_specific_salt = false;

			if (exlog_get_option('external_login_option_db_salting_method') == 'all') {
				$user_specific_salt =  $userData[$db_data["dbstructure_salt"]];
			}

            $hashFromDatabase = $userData[$db_data["dbstructure_password"]];
            if (has_filter(EXLOG_HOOK_FILTER_AUTHENTICATE_HASH)) {
                $valid_credentials = apply_filters(
                    EXLOG_HOOK_FILTER_AUTHENTICATE_HASH,
                    $password,
                    $hashFromDatabase,
                    $username,
                    $userData
                );
            } else {
                $valid_credentials = exlog_validate_password($password, $hashFromDatabase, $user_specific_salt);
            }

			if ($valid_credentials) {
				$wp_user_data = exlog_build_wp_user_data($db_data, $userData);
				$wp_user_data["exlog_authenticated"] = true;
				return $wp_user_data;
			} else {
				$user_data["exlog_authenticated"] = false;
				return $userData;
			}
		} else {
			return false;
		}
	}
	catch (Exception $ex) {
        error_log('EXLOG: Unable to complete database query:');
        error_log(var_export($ex, true));
        return false;
	}
}

function exlog_test_query($limit = false) {
	try {
		$dbType = exlog_get_option('external_login_option_db_type');

		$db_data = exlog_get_external_db_instance_and_fields($dbType);

        if ($db_data == false) {
            return false;
        }

		if($dbType == "mssql") {
			$query_string = "";
			
			if ($limit && is_int($limit)) {
				$query_string .= 'SELECT TOP ' . $limit . ' *';
			} else {
				$query_string .= 'SELECT *';
			}

			$query_string .= ' FROM ' . esc_sql($db_data["dbstructure_table"]);

			$stmt = sqlsrv_query( $db_data["db_instance"], $query_string);
			
			if (sqlsrv_has_rows( $stmt ) != true) {
				error_log("EXLOG: No rows returned from test query.");
				return false;
			}

			$users = array();
			
			while( $user_data = sqlsrv_fetch_array( $stmt))
			{
				array_push($users, exlog_build_wp_user_data($db_data, $user_data));
			}
			
			return $users;
		}
		else if ($dbType == "postgresql") {
			$query_string =
				'SELECT *' .
				' FROM "' . esc_sql($db_data["dbstructure_table"]) . '"';

			if ($limit && is_int($limit)) {
				$query_string .= ' LIMIT ' . $limit;
			}

			$rows = pg_query($query_string) or die('Query failed: ' . pg_last_error());

			$users = array();
			if (sizeof($rows) > 0) {
				while ($x = pg_fetch_array($rows, null, PGSQL_ASSOC)) {
					array_push($users, $x); //Gets the first row
				};
				pg_close($db_data["db_instance"]);
				return $users;
			}

		} else {
			$query_string =
				'SELECT *' .
				' FROM ' . esc_sql($db_data["dbstructure_table"]) . '';

			if ($limit && is_int($limit)) {
				$query_string .= ' LIMIT ' . $limit;
			}

			$rows = $db_data["db_instance"]->get_results($query_string, ARRAY_A);

			$users = array();
			if (sizeof($rows) > 0) {
				foreach ($rows as $user_data) {
					array_push($users, exlog_build_wp_user_data($db_data, $user_data));
				};
				return $users;
			}
		}

		//If got this far, query failed
		error_log("EXLOG: No rows returned from test query.");
		return false;
	}
	catch (Exception $ex) {
        error_log('EXLOG: Unable to make test query:');
        error_log(var_export($ex, true));
        return false;
	}
}

function exlog_build_exclude_query_string_component($db_data) {
    $dbType = exlog_get_option('external_login_option_db_type');
    $exclude_users_data = exlog_get_option('exlog_exclude_users_field_name_repeater');

    $exclude_query_string_section = "";
    if (gettype($exclude_users_data) == 'array') {
        foreach ($exclude_users_data as $field) {
            $field_name = $field['exlog_exclude_users_field_name'];
            if (!exlog_check_if_field_exists($db_data, $field_name)) {
                continue;
            }

            $field_values = $field['exlog_exclude_users_field_value_repeater'];
            foreach ($field_values as $value_object) {
                $value = $value_object['exlog_exclude_users_field_value'];
                if ($dbType == "mssql") {
					$string_part = ' AND ' . esc_sql($field_name) . ' != \'' . esc_sql($value) . '\'';
				}
				else if ($dbType == "postgresql") {
                    $string_part = ' AND "' . esc_sql($field_name) . '"::text NOT ILIKE \'' . esc_sql($value) . '\'';
                } else {
                    $string_part = ' AND NOT ' . esc_sql($field_name) . '="' . esc_sql($value) . '"';
                }
                $exclude_query_string_section .= $string_part;
            }
        }
    }
    return $exclude_query_string_section;
}

function exlog_check_if_field_exists($db_data, $field) {
    $dbType = exlog_get_option('external_login_option_db_type');
    if ($dbType == "mssql") {
		$query_string = "SELECT 1 FROM sys.columns WHERE Name = \'" . $field . "\' AND Object_ID = Object_ID(\'". esc_sql($db_data["dbstructure_table"]) ."\'";
        $result = $db_data["db_instance"]->get_results($query_string, ARRAY_A);
        return !empty($result);
	}
	else if ($dbType == "mysql") {
        $query_string = "SHOW COLUMNS FROM `" . esc_sql($db_data["dbstructure_table"]) . "` LIKE '" . $field . "';";
        $result = $db_data["db_instance"]->get_results($query_string, ARRAY_A);
        return !empty($result);
    } else {
        $query_string = "SELECT column_name FROM information_schema.columns WHERE table_name='" . $db_data["dbstructure_table"] ."' and column_name='" . $field . "';";
        $query_results = pg_query($query_string) or error_log("EXLOG: External DB query failed when checking if Field Exists");
        $result = pg_fetch_array($query_results, null, PGSQL_ASSOC);
        return is_array($result);
    }
}
