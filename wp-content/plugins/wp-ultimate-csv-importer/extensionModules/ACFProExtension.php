<?php
/**
 * WP Ultimate CSV Importer plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

namespace Smackcoders\FCSV;

if ( ! defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

class ACFProExtension extends ExtensionHandler{
    private static $instance = null;
	
    public static function getInstance() {
		
		if (ACFProExtension::$instance == null) {
			ACFProExtension::$instance = new ACFProExtension;
		}
		return ACFProExtension::$instance;
	}
	
	/**
	* Provides default mapping fields for ACF Pro plugin
	* @param string $data - selected import type
	* @return array - mapping fields
	*/
	public function processExtension($data){
		$response = [];
		$response['acf_pro_fields'] = null;
		$response['acf_group_fields'] = null;
		$response['acf_repeater_fields'] = null;	
		return $response;		
	}

	/**
	* ACF Pro extension supported import types
	* @param string $import_type - selected import type
	* @return boolean
	*/
	public function extensionSupportedImportType($import_type){
		
		if(is_plugin_active('advanced-custom-fields-pro/acf.php')){
			if($import_type == 'nav_menu_item'){
				return false;
			}

			$import_type = $this->import_name_as($import_type);
			
			if($import_type =='Posts' || $import_type =='Pages' || $import_type =='CustomPosts' || $import_type =='Users' || $import_type =='WooCommerce'|| $import_type =='Taxonomies' || $import_type =='Tags' || $import_type =='Categories') {		
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	
}
