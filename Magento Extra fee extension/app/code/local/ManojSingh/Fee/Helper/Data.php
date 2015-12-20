<?php
class ManojSingh_Fee_Helper_Data extends Mage_Core_Helper_Abstract
{
	public static function getFee(){
		$feeAmt = Mage::getStoreConfig('fee/general/fee_amt');
		return $feeAmt;
	}
	
	public static function canApply($address){
		$setFee = Mage::getStoreConfig('fee/general/active');
		if($setFee==1)
		{
			return true;
		}
	}
	
	public static function getFeeLabel(){
		$feeLabel = Mage::getStoreConfig('fee/general/fee_label');
		if(!$feeLabel){
			$feeLabel = Mage::helper('fee')->__('Additional Charge');
		}
		return $feeLabel;
	}
}