<?php
class Manojsingh_Productquestions_Helper_Producttab extends Mage_Core_Helper_Abstract {
    /*
     * Returns Product Edit Productquestions Tab params
     * @return array
     */

    public static function getTabparams() {
        $frontName = Mage::app()->getConfig()->getNode('admin/routers/productquestions_admin/args/frontName');
        $productId = Mage::app()->getRequest()->getParam('id');
        $_isSecure = Mage::getStoreConfig('web/secure/use_in_adminhtml');
        return array(
            'label' => Mage::helper('catalog')->__('Product Questions'),
            'url' => Mage::getUrl($frontName . '/adminhtml_index/', array('id' => $productId, '_current' => true, '_secure' => $_isSecure)),
            'class' => 'ajax',
        );
    }

}