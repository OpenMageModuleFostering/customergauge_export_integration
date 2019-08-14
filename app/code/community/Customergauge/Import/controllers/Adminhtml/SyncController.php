<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Adminhtml_SyncController extends Mage_Adminhtml_Controller_Action
{
  	public function indexAction(){
		 $this->loadLayout()
            ->_setActiveMenu( 'Customergauge_Import/adminhtml_sync' )
            ->_addContent( $this->getLayout()->createBlock( 'import/sync_edit' ) )
            ->renderLayout();
	}

	/**
	 * @author Manas Kanti Dey
	 * @desc
	 * Save CustomerGauge Mapping inside magento database.
	 * Redirect to index page.
	 *
	 */
	public function saveAction()
    {
    	
        $model = Mage::getModel( 'import/cgmapping' );

		$old_data = $model->getCollection()->getData();
		$mapping  = array();
		foreach( $old_data as $item ){
			$mapping[ $item[ "id" ] ] = $item[ "magento_field" ];
		}
		
		$templateLine = Mage::helper("import")->loadTemplate();
		foreach( $templateLine as $key => $val ){
			
			$post_val =	$this->getRequest()->getPost( $key );
			$post_val = trim( $post_val );
			if( $post_val != "" ){
				
				$id = array_search( $key, $mapping ); 
				$data = array( 'magento_field'=> trim( $key ), 'cg_field'=> trim( $post_val ) );
				if( $id !== FALSE ){
					$model->load($id)->addData( $data );
					$model->setId($id)->save();
				} else {
					$model->setData( $data );
					$model->save();
				}
				
			} else {
				
				$id = array_search( $key, $mapping );
				if( $id != "" ){
					$model->setId($id)->delete();
				}
				
			}
				
		}
		$this->_getSession()->addSuccess( $this->__('Mapping have been saved') );
		$this->_redirect('*/*/index');
		
    }
	
}

?>