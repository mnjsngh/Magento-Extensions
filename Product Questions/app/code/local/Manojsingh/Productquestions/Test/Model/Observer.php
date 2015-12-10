<?php
class Manojsingh_Productquestions_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::getModel('productquestions/observer');
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function updateProductQuestionsProductsNames() {
        $observer = new Varien_Object();
        $event = new Varien_Object();
        $store = Mage::getModel('core/store');
        $product = Mage::getModel('catalog/product');
        $product->setId(12);
        $product->setName('Product');
        $product->setStore($store);
        $event->setProduct($product);
        $observer->setEvent($event);
        $this->assertEquals(2, $this->_data->updateProductQuestionsProductsNames($observer));
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function deleteProductQuestionsForProduct() {
        $observer = new Varien_Object();
        $event = new Varien_Object();
        $store = Mage::getModel('core/store');
        $product = Mage::getModel('catalog/product');
        $product->setId(12);
        $product->setStore($store);
        $event->setProduct($product);
        $observer->setEvent($event);
        $this->assertEquals(2, $this->_data->deleteProductQuestionsForProduct($observer));
    }

}
