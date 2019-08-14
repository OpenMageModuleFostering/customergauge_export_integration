<?php
/**
 * @category    Customergauge
 * @package     Customergauge_Import
 * @author 		Manas Kanti Dey
 * @copyright   Copyright (c) 2013 CustomerGauge (http://www.customergauge.com)
 *
 */
class Customergauge_Import_Service_ImportData {

    private $_orderIds = array();
    private $_collectionOrders;
    private $_contentXML;

    public function __construct($ordersId) {
        $this->_orderIds = $ordersId;
    }

    private function _loadOrderObjects()
    {
        $this->_collectionOrders = array();

        foreach($this->_orderIds as $id) {
            $instance = Mage::getModel("sales/order")->load($id);
            array_push($this->_collectionOrders, $instance);
        }
    }

    /**
     * @author Manas Kanti Dey
     * @desc
     * It generate the xml for upload with mapping array
     *
     * @param
     * $mapping array
     * 
     * @return void
     */
    private function _prepareData( $mapping )
    {
        $this->_contentXML = "";
		 $product    = array();
        foreach( $this->_collectionOrders as $order ) {

            $lineItem = "";

            // iterate on the itens in template
            $orderItems = $order->getItemsCollection();
			$itemInc = 0;
	        foreach ( $orderItems as $item )
	        {
	           if ( ! $item->isDummy() ) {
	                $single_product = $this->getOrderItemValues( $item, $order, ++$itemInc );
					foreach( $single_product as $key => $val ){
						$product[ $key ][] = $val;
					}
				}

	        }
	        $shippingAddress = !$order->getIsVirtual() ? $order->getShippingAddress() : null;
	        $billingAddress = $order->getBillingAddress();
							
            foreach( $mapping as $fields ) {

                // order.increment_id => $order->getData("increment_id");
                // getAttributeByCode($attribute, $order)
                $item = "";
				$magento_field  = $fields[ "magento_field" ];
				$cg_field       = $fields[ "cg_field" ];
				$pos = strpos( $magento_field, "_" );
				$object 	= strtolower( substr( $magento_field, 0, $pos ) );
				$attribute 	= substr( $magento_field, $pos+1, strlen( $magento_field ) );
				
				
       			
		        $itemInc    = 0;

                switch( $object ) {

                    case "order":
                        $item = $order->getData( $attribute );
                        break;

                    case "customer":
                        if ($attribute=="name") {
                            $item = $order->getData("customer_firstname") . " " .
                            $order->getData("customer_lastname");
                        } else {
                            $item = $order->getData("customer_{$attribute}");
                        }
                    break;

                    case "shipping":
                    	
                        if (strpos($attribute, "street_")!==false) {
                            $street = explode("_", $attribute);
                            $item = $shippingAddress->getStreet( $street[1] );
                        } else {
                            $item = $shippingAddress->getData( $attribute );
                        }
                    break;
					
					case "billing":
                        if (strpos($attribute, "street_")!==false) {
                            $street = explode("_", $attribute);
                            $item = $billingAddress->getStreet( $street[1] );
                        } else {
                            $item = $billingAddress->getData( $attribute );
                        }
                    break;
					
					case "item":
                        if ( array_key_exists( $attribute, $product ) ) {
						    $item = implode( ";", $product[ $attribute ] );
						} else {
                        	$item = "";
                        }
                    break;
                }

                $lineItem .= "<{$cg_field}>{$item}</{$cg_field}>";
            }

            // endline
           $this->_contentXML .= "<RECORD>{$lineItem}</RECORD>";
        }
    }

	protected function getOrderItemValues( $item, $order, $itemInc=1 ) 
    {
        return array(
            'increment' => $itemInc,
            'name'      => $item->getName(),
            'status'    => $item->getStatus(),
            'sku'       => $this->getItemSku($item),
            'option'    => $this->getItemOptions($item),
            'original_price' => $this->formatPrice($item->getOriginalPrice(), $order),
            'price' => $this->formatPrice($item->getData('price'), $order),
            'qty_ordered'  => (int)$item->getQtyOrdered(),
            'qty_invoiced' => (int)$item->getQtyInvoiced(),
            'qty_shipped'  => (int)$item->getQtyShipped(),
            'qty_canceled' => (int)$item->getQtyCanceled(),
        	'qty_refunded' => (int)$item->getQtyRefunded(),
            'tax' 		=> $this->formatPrice($item->getTaxAmount(), $order),
            'discount' 	=> $this->formatPrice($item->getDiscountAmount(), $order),
            'total' 	=> $this->formatPrice($this->getItemTotal($item), $order)
        );
    }
    
    /**
     * @author Manas Kanti Dey
     * @desc
     * It upload the on CustomerGauge using apiCall method
     *
     * @param
     * $mapping array
     *
     * @return void
     */
    public function call()
    {
        $this->_loadOrderObjects();
        $model   = Mage::getModel( 'import/cgmapping' );
        $mapping = $model->getCollection()->getData();
        $this->_prepareData( $mapping );
		$post_xml = '<?xml version="1.0" encoding="utf-8"?><RECORDS>' . $this->_contentXML . '</RECORDS>';
		
		$config_data = Mage::getStoreConfig('import/config');
		$is_prodcution_active	= $config_data[ "cg_activepro" ];
		if( $is_prodcution_active )
			$api_url = $config_data[ "cg_prourl" ];
		else {
			$api_url = $config_data[ "cg_devurl" ];
		}
		$api_key = $config_data[ "cg_apikey" ];
		$api_url = $api_url . "upload_record.json?api_key=$api_key";
	    $json_data = Mage::helper("import")->apiCall( $api_url, $post_xml );
        return $json_data;
    }

	protected function getItemSku($item)
    {
        if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $item->getProductOptionByCode('simple_sku');
        }
        return $item->getSku();
    }

    protected function getItemOptions($item)
    {
        $options = '';
        if ($orderOptions = $this->getItemOrderOptions($item)) {
            foreach ($orderOptions as $_option) {
                if (strlen($options) > 0) {
                    $options .= ', ';
                }
                $options .= $_option['label'].': '.$_option['value'];
            }
        }
        return $options;
    }
	
	protected function getItemOrderOptions($item)
    {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }
        return $result;
    }
	
	protected function getItemTotal($item) 
    {
        return $item->getRowTotal() - $item->getDiscountAmount() + $item->getTaxAmount() + $item->getWeeeTaxAppliedRowAmount();
    }
	
	protected function formatPrice($price, $formatter) 
    {
    	return $formatter->formatPriceTxt($price);
    }

}