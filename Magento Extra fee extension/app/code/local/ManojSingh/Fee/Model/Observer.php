<?php
class ManojSingh_Fee_Model_Observer{
	public function invoiceSaveAfter(Varien_Event_Observer $observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		if ($invoice->getBaseFeeAmount()) {
			$order = $invoice->getOrder();
			$order->setFeeAmountInvoiced($order->getFeeAmountInvoiced() + $invoice->getFeeAmount());
			$order->setBaseFeeAmountInvoiced($order->getBaseFeeAmountInvoiced() + $invoice->getBaseFeeAmount());
		}
		return $this;
	}
	public function creditmemoSaveAfter(Varien_Event_Observer $observer)
	{
		/* @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
		$creditmemo = $observer->getEvent()->getCreditmemo();
		if ($creditmemo->getFeeAmount()) {
			$order = $creditmemo->getOrder();
			$order->setFeeAmountRefunded($order->getFeeAmountRefunded() + $creditmemo->getFeeAmount());
			$order->setBaseFeeAmountRefunded($order->getBaseFeeAmountRefunded() + $creditmemo->getBaseFeeAmount());
		}
		return $this;
	}
	public function updatePaypalTotal($observer){
        $paypalCart = $observer->getEvent()->getPaypalCart();
        $additional = $observer->getEvent()->getAdditional();
        $salesEntity = $observer->getEvent()->getSalesEntity();
        if ($additional instanceof Varien_Object && $salesEntity instanceof Mage_Core_Model_Abstract) {
            if ($salesEntity->getBaseFeeAmount() != 0) {
                $items = $additional->getItems();
				$name = Mage::helper('fee')->getFeeLabel();
                $items[] = new Varien_Object(
                    array(
                         'id'     => $this->_convertDescriptionToId($name),
                         'name'   => $this->_getDescription(),
                         'qty'    => 1,
                         'amount' => round($salesEntity->getBaseFeeAmount(), 2),
                    )
                );
                $salesEntity->setBaseSubtotal(
                    $salesEntity->getBaseSubtotal() + $salesEntity->getBaseFeeAmount()
                );
                $additional->setItems($items);
            }
        } elseif ($paypalCart) {
            if ($paypalCart->getSalesEntity()->getBaseFeeAmount() > 0) {
				$name = Mage::helper('fee')->getFeeLabel();
                $paypalCart->addItem(
                    $this->_getDescription(),
                    1,
                    $paypalCart->getSalesEntity()->getBaseFeeAmount(),
                    $this->_convertDescriptionToId($name)
                );
                if ($paypalCart->isShippingAsItem()) {
                    //if shipping is added as line item - the above addItem('surcharge') will make shipping count twice
                    $paypalCart->updateTotal(
                        Mage_Paypal_Model_Cart::TOTAL_SUBTOTAL,
                        -1 * $paypalCart->getSalesEntity()->getBaseShippingAmount()
                    );
                }
            }
        }
	}
	
	protected function _convertDescriptionToId($description)
    {
        $description = preg_replace(
            "/[^a-z0-9]+/", "", strtolower($description)
        );
        if (empty($description)) {
            return Mage::helper('fee')->__('additional_charge');
        } else {
            return $description;
        }
    }
	
	protected function _getDescription()
    {
        $label = Mage::helper('fee')->getFeeLabel();
        return $this->_escapeHtmlByVersion($label);
    }
	
	protected function _escapeHtmlByVersion($input, $allowedTags = null)
    {
        $helper = Mage::helper('core');
        if (method_exists($helper, 'escapeHtml')) {
            return Mage::helper('core')->escapeHtml($input, $allowedTags);
        } else {
            return Mage::helper('core')->htmlEscape($input, $allowedTags);
        }
    }

}