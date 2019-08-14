<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Model_System_Config_Upload
{
	/**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	
		$values = array(
			array('value' => 0, 'label'=> "Development" ) ,
			array('value' => 1, 'label'=> "LIVE Production" )
		);
		return $values;
		
    }
}