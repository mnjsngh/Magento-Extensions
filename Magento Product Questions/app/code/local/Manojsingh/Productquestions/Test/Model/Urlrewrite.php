<?php
class Manojsingh_Productquestions_Test_Model_Urlrewrite extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::getModel('productquestions/urlrewrite');
    }

    /**
     * @test
     * @loadFixture product
     * @loadFixture questions
     */
    public function rewrite() {
        $request = new Zend_Controller_Request_Http();
        $response = new Zend_Controller_Response_Http();
        $request->setPathInfo('productquestions/index/index/id/12/category/2/-questions.html');
        $result = $this->_data->rewrite($request, $response);
        $this->assertEquals(false, $result);
    }

}