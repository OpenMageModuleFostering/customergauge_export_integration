<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Model_System_Config_Orderstatus
{
	/**
     * @desc
     * Get all the Sales order status
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
    	$all_status = Mage::getModel('sales/order_status')->getResourceCollection()->getData();
    	foreach( $all_status as $status ){
 			$values[] = array( 'value' => $status[ 'status' ], 'label'=> $status[ 'label' ] );
     	} 
		return $values;
		
    }
}