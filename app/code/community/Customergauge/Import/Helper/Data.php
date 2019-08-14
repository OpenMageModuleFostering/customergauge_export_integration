<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @author Manas Kanti Dey
     * @desc
     * Get array of items in template line
     * 
     * @return array
     */ 
    public static function loadTemplate()
    {
    	
    	$config_data = Mage::getStoreConfig('import/config');
    	$is_prodcution_active	= $config_data[ "cg_activepro" ];
    	if( $is_prodcution_active )
    		$api_url = $config_data[ "cg_prourl" ];
    	else {
    		$api_url = $config_data[ "cg_devurl" ];
    	}
    	$api_key = $config_data[ "cg_apikey" ];
    	$api_url = $api_url . "get_magento_field.json?api_key=$api_key";
    	$json_data = Mage::helper("import")->apiCall( $api_url );
    	$json_array = json_decode( $json_data, true );
    	if( $json_array[ "IsSuccess" ] ){
    		
    		return $json_array[ "Data" ];
    		
    	} else {
    		
    	}
    		
        return $magento_field;
        
    }
	
    /**
     * @author Manas Kanti Dey
     * @desc
     * It calls the customergauge api, if the uploaded data given then it post the data on API URL
     *
     * @return array
     */
	public static function apiCall( $apiUrl, $upload_data = "" ) {

		$curl_handle = curl_init( );
		curl_setopt($curl_handle, CURLOPT_URL, $apiUrl);
		if( isset( $upload_data ) )
		{
			curl_setopt( $curl_handle, CURLOPT_POST, true );
			curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $upload_data );
		}
		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $curl_handle, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
		curl_setopt( $curl_handle, CURLOPT_TIMEOUT, 1000);
		$curl_data = curl_exec( $curl_handle );
		curl_close( $curl_handle );		
		return $curl_data;
		
	}
	
}