<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Model_Observer {
    
    /**
     * @author Manas Kanti Dey
     * 
     * @desc
     * This method will include a option in Action Select List of order page
     */
    public function includeOption($observer)
    {
    	
        $idBlockObserver = $observer->getEvent()->getBlock()->getId();
        if ( $idBlockObserver=="sales_order_grid" ) {
            
            $block = $observer->getEvent()
                ->getBlock()
                ->getMassactionBlock();
            
            if ($block) {
                $block->addItem('import', array(
                    'label'=> Mage::helper('import')->__('Send to CustomerGauge'),
                    'url'  => Mage::getUrl('customergauge_import', array('_secure'=>true)),
                ));
            }
			
        }
    }

    
    /**
     * @author Manas Kanti Dey
     * 
     * @desc
     * This method will call by magento after each save on order
     * 
     */
    public function hookToOrderSaveEvent($observer)
    {
    		
    	$config_data 			= Mage::getStoreConfig('import/config');
    	$is_cg_transactional	= $config_data[ "cg_transactional" ];
    	
    	if( $is_cg_transactional ){
    		
    	
    		$order = $observer->getEvent()->getOrder();
    		$order_id		= $order->getId();
    		$orderIdsList[] = $order_id;
    		$order_status   = $order->getStatus();
    		$customergauge_import = $order->getData( "customergauge_import" );
			
    		$cg_orderstatusmagento  = $config_data[ "cg_orderstatusmagento" ];
    		if( $cg_orderstatusmagento == $order_status && $customergauge_import == "Not Imported" ){
    			
    			$CustomergaugeImport = new Customergauge_Import_Service_ImportData( $orderIdsList );
    			$api_response = $CustomergaugeImport->call();
    			$api_reponse_array = json_decode( $api_response, true );
    			 
    			if( $api_reponse_array[ 'IsSuccess' ] == 1 ){
    			
    				$response_data = $api_reponse_array[ 'Data' ][ 'log' ];
    				if( ! $response_data[0]['error'] ){
    						
    					$data = array('customergauge_import'=>'Imported');
    					$orderModel = Mage::getModel('sales/order')->load($order_id)->addData($data);
    					try {
    						$orderModel->setId($order_id)->save();
    					} catch (Exception $e){}
    						
    				}
    			
    			}
    			
    		}
    		
    			
    	}
    		
    }
	
}