<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Block_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

	
	protected function _prepareColumns()
	{
	   $options = array(
				'Imported' => 'Imported',
				'Not Imported' => 'Not Imported'
		);
	    $this->addColumn('customergauge_import', array(
	        'header'=> Mage::helper('customer')->__('Customergauge Import'),
	        'width' => '80px',
	        'type'  => 'options',
	        'index' => 'customergauge_import',
	    	'options'   =>   $options ));
	    $this->addColumnsOrder('customergauge_import', 'grand_total');
	    return parent::_prepareColumns();
	
	}

}