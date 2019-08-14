<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Model_Mysql4_Cgmapping extends Mage_Core_Model_Mysql4_Abstract
{
    
    public function _construct()
    {    
        $this->_init('import/cgmapping', 'id');
    }
  
  
}