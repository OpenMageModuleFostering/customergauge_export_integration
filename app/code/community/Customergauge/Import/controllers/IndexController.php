<?php
/**
 * Magento CustomerGauge Import Module
 *
 * NOTICE OF LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category   Customergauge
 * @package    Customergauge_Import
 * @copyright  Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 * @author     Manas Kanti Dey <manas.dey@directness.net>
 * */
require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Customergauge_Import_IndexController 
    extends Mage_Adminhtml_Sales_OrderController {

        public function indexAction()
        {
             $post = $this->getRequest()->getPost();
			 $orderIdsList = $post['order_ids'];

          	 $CustomergaugeImport = new Customergauge_Import_Service_ImportData( $orderIdsList );
             $api_response = $CustomergaugeImport->call();
			 $api_reponse_array = json_decode( $api_response, true );
			 
             if( $api_reponse_array[ 'IsSuccess' ] == 1  ){
             	
             	$i=0;
             	$response_data = $api_reponse_array[ 'Data' ][ 'log' ];
             	foreach( $orderIdsList as $orderId){

             		if( ! $response_data[$i]['error'] ){
             		
             			$data = array('customergauge_import'=>'Imported');
             			$orderModel = Mage::getModel('sales/order')->load($orderId)->addData($data);
             			try {
             				$orderModel->setId($orderId)->save();
             			} catch (Exception $e){}
             		}
             		$i++;
             		
             	}
				$successCount = $api_reponse_array[ 'Data' ][ 'total_successfull_record' ];
				$unsuccessCount =	$api_reponse_array[ 'Data' ][ 'total_unsuccessfull_record' ];
				if( !empty( $successCount ) && $successCount > 0  )
					$this->_getSession()->addSuccess( $this->__( '%s order(s) have been imported', $successCount ) );
				if( !empty( $unsuccessCount ) && $unsuccessCount > 0  )
					$this->_getSession()->addError( $this->__( '%s order(s) failed on import', $unsuccessCount ) ); 
				
             } else {
				  $this->_getSession()->addError( $this->__( $api_reponse_array[ "ExceptionMessage" ] ) );
			 }   
			 $this->_redirect( "adminhtml/sales_order" );  
        }
		
}