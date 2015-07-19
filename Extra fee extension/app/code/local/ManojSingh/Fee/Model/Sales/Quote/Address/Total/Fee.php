<?php
class ManojSingh_Fee_Model_Sales_Quote_Address_Total_Fee extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	protected $_code = 'fee';

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);

		$this->_setAmount(0);
		$this->_setBaseAmount(0);

		$items = $this->_getAddressItems($address);
		if (!count($items)) {
			return $this; //this makes only address type shipping to come through
		}

		$quote = $address->getQuote();
		$balance = 0;
		if(Mage::helper('fee')->canApply($address)){
			$exist_amount = $quote->getFeeAmount();
			$fee = Mage::helper('fee')->getFee();
			$balance = $fee - $exist_amount;		
		}
		$address->setFeeAmount($balance);
		$address->setBaseFeeAmount($balance);
		$this->_setAmount($balance);
		$this->_setBaseAmount($balance);
		
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		$setFee = Mage::getStoreConfig('fee/general/active');
		if($setFee==1)
		{
			$amt = $address->getFeeAmount();
			if($amt) {
				$address->addTotal(array(
						'code'=>$this->getCode(),
						'title'=>Mage::helper('fee')->getFeeLabel(),
						'value'=> $amt
				));
				return $this;
			}
		}
	}
}
