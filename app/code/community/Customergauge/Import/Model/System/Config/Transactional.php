<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Model_System_Config_Transactional
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
		$values = array(
			array('value' => 0, 'label'=> "Manual" ) ,
			array('value' => 1, 'label'=> "Automatic" )
		);
		return $values;
		
    }
}