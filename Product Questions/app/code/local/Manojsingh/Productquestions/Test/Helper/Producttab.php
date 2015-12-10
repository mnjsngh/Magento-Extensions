<?php
class Manojsingh_Productquestions_Test_Helper_Producttab extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::helper('productquestions');
    }

    /**
     * @test
     * @dataProvider provider_getparams
     */
    public function getTabparams($data) {
        $frontName = Mage::app()->getConfig()->getNode('admin/routers/productquestions_admin/args/frontName');
        Mage::app()->getRequest()->setParam('id', $data['id']);
        $productId = Mage::app()->getRequest()->getParam('id');

        $check = array(
            'label' => Mage::helper('catalog')->__('Productquestions'),
            'url' => Mage::getUrl($frontName . '/adminhtml_index/', array('id' => $productId, '_current' => true)),
            'class' => 'ajax',
        );
        $result = Manojsingh_Productquestions_Helper_Producttab::getTabparams();
        $this->assertEquals($check, $result);
    }

    public function provider_getparams() {
        return array(
            array(array('id' => '1')),
            array(array('id' => '2')),
            array(array('id' => ''))
        );
    }

}