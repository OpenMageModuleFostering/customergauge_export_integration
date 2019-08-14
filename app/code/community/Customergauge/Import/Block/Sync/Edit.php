<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Block_Sync_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected $_addButtonLabel = 'Add New Example';
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('customergauge/sync/edit.phtml');
		$this->setTitle('Field Mapping');

    }

    public function getSaveButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'customergauge_mapping',
            'label'     => $this->helper('adminhtml')->__('Save'),
            'onclick'   => 'javascript:document.getElementById(\'customgergauge_sync_form\').submit();'
        ));
 
        return $button->toHtml();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

	public function requiredDataExists(){
		$requiredData = Mage::getStoreConfig('import/config');
		$requiredKeys = array('cg_apikey');
		foreach($requiredKeys as $key){
			if(array_key_exists($key, $requiredData)){
				if( empty( $requiredData[ $key ] ) ){
					return false;
				}
			}else{
				return false;
			}
		}
		return true;
	}
	
	public function getMagentoField(){

		$templateLine = Mage::helper("import")->loadTemplate();
		$MagentoFieldArray = array();
		foreach( $templateLine as $key => $val ){
			$MagentoFieldArray[ $key ] = $val;
		}
		return $MagentoFieldArray;
		
	}
	
	public function getCustomergaugeField( ){
	
		$config_data = Mage::getStoreConfig('import/config');
		$is_prodcution_active	= $config_data[ "cg_activepro" ];
		if( $is_prodcution_active )
			$api_url = $config_data[ "cg_prourl" ];
		else {
			$api_url = $config_data[ "cg_devurl" ];
		}
		$api_key = $config_data[ "cg_apikey" ];
		$api_url = $api_url . "get_cg_field.json?api_key=$api_key";
	    $json_data = Mage::helper("import")->apiCall( $api_url );
		$json_array = json_decode( $json_data, true );
		if( $json_array[ "IsSuccess" ] )
			return $json_array[ "Data" ];
		
	}
	
	public function getCgMapping(){
		
		$model = Mage::getModel( 'import/cgmapping' );
		$data  = $model->getCollection()->getData();
		return $data;
		
	}
	
	public function CheckSlected( $cgmapping, $val ){
		
		foreach( $cgmapping as $item ){
			
			if( $item[ "magento_field" ] == $val )
				return $item[ "cg_field" ];
		}
		return "";
	}
}