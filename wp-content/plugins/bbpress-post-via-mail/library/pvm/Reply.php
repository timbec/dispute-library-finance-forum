<?php

use \EmailReplyParser\EmailReplyParser;

function log_info($v) {
    error_log("bbPress-pvm: $v");
}

function debug_dump($v) {
    if (IsDebugMode()) {
        $o = print_r($v, true);
        if (php_sapi_name() == "cli") {
            print( "$o\n");
        } else {
            //flush the buffers
            while (ob_get_level() > 0) {
            ob_end_flush();
            }
            print( "<pre>\n");
            EchoInfo($o);
            print( "</pre>\n");
       }
   }
}

function filename_fix($filename) {
    return str_replace('%', '', urlencode($filename));
}

class pvm_Reply {
	public $from;
	public $subject;
	public $body;
	public $attachments;
    public $nonce;
	public $post;
	public $site;
        
	public function __construct() {
	}
    function filename_fix($filename) {
        return str_replace('%', '', urlencode($filename));
    }

	public function parse_body() {
		// Parse the body and remove signatures, and reformat
		$parts = array();
        $email = EmailReplyParser::read($this->body);
        $fragments = $email->getFragments();
		foreach ($fragments as $fragment) {
			// We don't care about hidden parts (signatures, eg)
            // $debug_export = var_export($fragment, true);
			if ($fragment->isHidden()) {
				continue;
			}
			elseif ($fragment->isQuoted()) {
				// Remove leading quote symbols
				$quoted = preg_replace('/^> */m', '', $fragment->getContent());
				// Reparse to ensure that we strip signatures from here too
				$subfragments = EmailReplyParser::read($quoted);
				$subparts = array();
				foreach ($subfragments as $subfrag) {
					if ($subfrag->isHidden()) continue;
					$subparts[] = $subfrag->getContent();
				}
				$parts[] = '<blockquote>' . implode("\n", $subparts) . '</blockquote>';
			}
			else $parts[] = $fragment->getContent();
		}
		$content = implode("\n", $parts);
		return $content;
	}
    
    public function parse_attachments() {
	    function prepare_error_msg($reason,$valueOK=0,$value=0,$filename='') {
            $error_msg = $reason;
            if ($valueOK) {
                $error_msg .= " (".$value.' ';
                $error_msg .= __("instead of",'pvm');
                $error_msg .= " ".$valueOK;
                if ($filename) {
                    $error_msg .= " ";
                    $error_msg .= __("for attachment ",'pvm');
                    $error_msg .= ": ".$filename;
                }
                $error_msg .= ")";
            }
            return $error_msg;
        }
        function remove_comment($option) {
            $arr = explode("\n",$option);
            foreach ($arr as $key => $type) {
                $i = strpos($type,'#');
                if ($i !== false)
                    $arr[$key] = substr($type,0,$i);
            }
            return implode("\n",$arr);
        }
    	$upload_dir = wp_upload_dir();
	//	$action = 'wp_handle_upload';
        $attachment_list = array ();
        $errors = array ();
        $process_attachments = pvm::get_option('bb_pvm_attachments', '');
        $max_attachment_num  = pvm::get_option('bb_pvm_attachments_num', '');
        $max_attachment_size = pvm::get_option('bb_pvm_attachments_size', '')*1024;
        $attachments_allowed = remove_comment(pvm::get_option('bb_pvm_attachments_allowed', ''));
        $attachments_ignored = remove_comment(pvm::get_option('bb_pvm_attachments_ignored', ''));
        if (!$process_attachments)
            return;
        $debug_export = var_export($this->attachments,true);
        error_log("Attachments: ".$debug_export);
        foreach($this->attachments as $attachment) {
            $filename = $this->filename_fix($attachment->Name);
            //error_log("Processing: ".$filename);
            $filename = wp_unique_filename($upload_dir["path"], $filename);
            $path_parts = pathinfo($filename);
            //$debug_export = var_export($path_parts,true);
            $attachment_name = $path_parts['filename'];
            $attachment_ext  = ".".$path_parts['extension'];
            //error_log("ext: ".$attachment_ext);
            if (strpos($attachments_ignored,$attachment_ext)!==false) {
                //error_log(prepare_error_msg(__('Attachment','pvm').' '.$attachment->Name.' '.__("ignored by extension: ",'pvm').$attachment_ext));
                continue;
            }
            //error_log("Attachment mime type: ". $attachment->ContentType);
            //error_log("Strpos: ".strpos($attachments_ignored,$attachment->ContentType)===false);
            if (strpos($attachments_ignored,$attachment->ContentType)!==false) {
                //error_log(prepare_error_msg(__('Attachment','pvm').' '.$attachment->Name.' '.__("ignored by MIME type: ",'pvm').$attachment->ContentType));
                continue;
            }
            if ((strpos($attachments_allowed,$attachment_ext) === false) && (strpos($attachments_allowed,$attachment->ContentType) === false)) {
                $errors[] = prepare_error_msg(__('Attachment','pvm').' '.$attachment->Name.' '.__("is a file of banned type (not allowed by extension nor Mime type)",'pvm')." ".$attachment->ContentType);
                //error_log(prepare_error_msg(__('Attachment','pvm').' '.$attachment->Name.' '.__("not allowed by extension nor Mime type",'pvm')." ".$attachment->ContentType));
                continue;
            }
            if (count($attachment_list) >= $max_attachment_num) {
                $errors[] = prepare_error_msg(__('Maximum number of attachments exceeded, ommiting remaining','pvm'),$max_attachment_num,count($attachment_list)+1);
                break;
            }
            if ($attachment->ContentLength > $max_attachment_size) {
                $errors[] = prepare_error_msg(__('Maximum size of attachment exceeded, skipped this one','pvm'),$max_attachment_size,$attachment->ContentLength,$attachment->Name);
                //error_log(prepare_error_msg(__('Maximum size of attachment exceeded, skipped this one','pvm'),$max_attachment_size,$attachment->ContentLength,$attachment->Name));
                continue;;
            }
			$tmpFile = tempnam(get_temp_dir(), 'pvm-');
    	    if ($tmpFile !== false) {
        		$downloaded_size = $attachment->DownloadToFile($tmpFile);
        		if ($downloaded_size != $attachment->ContentLength) {
					error_log("Problem downloading file ".$tmpFile. "for attachment ".$attachment->Name."Downloaded".$downloaded_size." instead of ".$attachment->ContentLength);
			        continue;
                }
            }
            
        	// Move the file to the uploads dir
        	$new_file = $upload_dir["path"] . "/$filename";
        	if (false === rename($tmpFile, $new_file)) {
                error_log("Rename failed new file: ". $new_file. " old file: ".$tmpFile);
                continue ;
        	}
            // Set correct file permissions
    	    $stat = stat(dirname($new_file));
    		$perms = $stat['mode'] & 0000666;
    		if (!chmod($new_file, $perms))
        		error_log("upload: permissions not changed". $new_file);
    		// Compute the URL
    		$url = $upload_dir['url'] . "/$filename";
    		$return = apply_filters('wp_handle_upload', array('file' => $new_file, 'url' => $url,'type' => $attachment->ContentType));
            $attachment_list[] = array ('post_mime_type'=>$attachment->ContentType,
                                        'guid'=>$url,
                                        'post_parent'=>0,
                                        'post_title'=>$attachment_name,
                                        'post_excerpt'=>'',
                                        'post_content'=>'',
                                        'post_author'=>1,
                                        'filename' => $new_file);
        }
        //$debug_export = var_export($attachment_list, true);
        //error_log("Attachment list: ". $debug_export);
        //error_log("Count: ".count($attachment_list));
        //$debug_export = var_export($errors, true);
        //error_log("Final error msg: ".$debug_export);
        return array ('attachments' => $attachment_list,'errors'=>$errors);
    }

	public function get_user() {
		return get_user_by( 'id', $this->user );
	}

	public function is_valid() {
		$user = $this->get_user();
		return $this->nonce === pvm::get_hash($this->post, $user, $this->site);
	}

	public function insert() {
		if ( is_multisite() ) {
			switch_to_blog( $this->site );
		}

		$result = apply_filters( 'pvm.reply.insert', null, $this );

		if ( is_multisite() ) {
			restore_current_blog();
		}

		return $result;
	}

	public static function parse_to($address) {
		$template = pvm::get_option('bb_pvm_replyto');

		// No plus address in saved, parse via splitting
		$has_match = preg_match( '/\+(\w+)-(\d+)-(\d+)-(\w+)\@.*/i', $address, $matches );
		if ( ! $has_match ) {
			throw new Exception('Reply-to not formatted correctly -'.$address);
                        throw new Exception($address);
		}
		return array( $matches[1], $matches[2], $matches[3], $matches[4] );
	}
}

