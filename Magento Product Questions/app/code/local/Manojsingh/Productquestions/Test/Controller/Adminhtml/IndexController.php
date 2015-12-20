<?php
class Manojsingh_Productquestions_Test_Controller_Adminhtml_IndexController extends EcomDev_PHPUnit_Test_Case_Controller {

    protected function _setUpAdminArea() {
        Mage::getSingleton('core/session', array('name' => 'adminhtml'));
        $user = Mage::getModel('admin/user')->loadByUsername('master');
        if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
            Mage::getSingleton('adminhtml/url')->renewSecretUrls();
        }
        $session = Mage::getSingleton('admin/session');
        $session->setIsFirstVisit(true);
        $session->setUser($user);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());
    }

    /**
     * @test
     * @doNotIndexAll
     * @loadFixture admin
     * @dataProvider provider__index
     */
    public function indexAction($data) {
        $this->_setUpAdminArea();
        $this->dispatch("productquestions_admin/adminhtml_index/index/{$data['ajax']}");
        $request = Mage::app()->getRequest();
        $this->assertEquals('index', $request->getActionName());
    }

    public function provider__index() {
        return array(
            array(array('ajax' => '')),
            array(array('ajax' => 'ajax/1')),
            array(array('ajax' => 'isAjax/1'))
        );
    }

    /**

     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     * @dataProvider provider__reply
     */
    public function replyAction($data) {
        $this->reset();
        $session = Mage::getSingleton('admin/session');
        $session->clear();
        $this->_setUpAdminArea();
        $this->dispatch("productquestions_admin/adminhtml_index/reply/id/{$data['id']}");
        $request = Mage::app()->getRequest();
        $this->assertEquals('reply', $request->getActionName());
    }

    public function provider__reply() {
        return array(
            array(array('id' => '')),
            array(array('id' => '1')),
            array(array('id' => '20'))
        );
    }

    /**
     * @test
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     * @dataProvider provider__save
     * Check saving reply with sending/not sending emails
     */
    public function saveAction($data) {
        $this->reset();
        $this->_setUpAdminArea();
        $session = Mage::getSingleton('adminhtml/session');
        if ($data['data'])
            Mage::app()->getRequest()->setPost($data['data']);
        Mage::app()->getStore(1)->setConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_SENDER, $data['sender']);
        $this->dispatch("productquestions_admin/adminhtml_index/save/id/{$data['id']}/sendEmail/{$data['sendEmail']}");
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        $this->assertEquals($data['expected'], $actual);
    }

    public function provider__save() {
        return array(
            array(array('id' => '1', 'data' =>
                    array(
                        'product_id' => '12',
                        'question_store_ids' => '1,2',
                        'question_author_name' => 'customer',
                        'question_author_email' => 'john@doe.com',
                        'question_date' => '2010-01-01-25:55:55',
                        'question_datetime' => '2010-01-01-25:55:55',
                        'reply_text' => 'reply-reply',
                        'question_status' => '1',
                        'question_store_id' => '1',
                        'question_text' => 'Question for product'), 'sendEmail' => '1', 'sender' => 'pq@admin.com', 'expected' => 'error: Message was successfully saved, but the email was not sent')),
            array(array('id' => '1', 'data' =>
                    array(
                        'product_id' => '12',
                        'question_store_ids' => '1,2',
                        'question_author_name' => 'customer',
                        'question_author_email' => 'john@doe.com',
                        'question_date' => '2010-01-01-25:55:55',
                        'question_datetime' => '2010-01-01-25:55:55',
                        'reply_text' => 'reply-reply',
                        'question_status' => '1',
                        'question_store_id' => '1',
                        'question_text' => 'Question for product'), 'sendEmail' => '1', 'sender' => '', 'expected' => 'success: Email was sent successfullysuccess: Question was successfully saved')),
            array(array('id' => '1', 'data' =>
                    array(
                        'product_id' => '12',
                        'question_store_ids' => '1,2',
                        'question_author_name' => 'customer',
                        'question_author_email' => 'john@doe.com',
                        'question_date' => '2010-01-01-25:55:55',
                        'question_datetime' => '2010-01-01-25:55:55',
                        'reply_text' => 'reply-reply',
                        'question_status' => '1',
                        'question_store_id' => '1',
                        'question_text' => 'Question for product'), 'sendEmail' => '', 'sender' => '', 'expected' => 'success: Question was successfully saved')),
            array(array('id' => '1', 'sendEmail' => '', 'sender' => '', 'expected' => 'error: Unable to find a data to save'))
        );
    }

    /**
     * @test
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     * @dataProvider provider__delete
     */
    public function deleteAction($data) {
        $this->reset();
        $this->_setUpAdminArea();
        $session = Mage::getSingleton('adminhtml/session');
        $this->dispatch("productquestions_admin/adminhtml_index/delete/id/{$data['id']}");
        $request = Mage::app()->getRequest();
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        $this->assertEquals($data['expected'], $actual);
        $this->assertEquals('delete', $request->getActionName());
    }

    public function provider__delete() {
        return array(
            array(array('id' => '', 'expected' => '')),
            array(array('id' => '1', 'expected' => 'success: Question was successfully deleted'))
        );
    }

    /**
     * @test
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     * @dataProvider provider__massdelete
     */
    public function massDeleteAction($data) {
        $this->reset();
        $this->_setUpAdminArea();
        Mage::app()->getRequest()->setParam('productquestions', $data['ids']);
        $session = Mage::getSingleton('adminhtml/session');
        $this->dispatch("productquestions_admin/adminhtml_index/massDelete/");
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        $this->assertEquals($data['expected'], $actual);
    }

    public function provider__massdelete() {
        return array(
            array(array('ids' => '', 'expected' => 'error: Please select question(s)')),
            array(array('ids' => array('0' => '1', '1' => '2'), 'expected' => 'success: Total of 2 question(s) were successfully deleted'))
        );
    }

    /**
     * @test
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     * @dataProvider provider__massStatus
     */
    public function massStatusAction($data) {
        $this->reset();
        $this->_setUpAdminArea();
        Mage::app()->getRequest()->setParam('productquestions', $data['ids']);
        $session = Mage::getSingleton('adminhtml/session');
        $this->dispatch("productquestions_admin/adminhtml_index/massStatus/");
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        $this->assertEquals($data['expected'], $actual);
    }

    public function provider__massStatus() {
        return array(
            array(array('ids' => '', 'expected' => 'error: Please select item(s)')),
            array(array('ids' => array('0' => '1', '1' => '2'), 'expected' => 'success: Total of 2 record(s) were successfully updated'))
        );
    }

    /**
     *
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     */
    public function exportCsvAction() {
        $this->reset();
        $this->_setUpAdminArea();
        $this->dispatch("productquestions_admin/adminhtml_index/exportCsv/");
        $request = Mage::app()->getRequest();
        $this->assertEquals('exportCsv', $request->getActionName());
    }

    /**
     *
     * @doNotIndexAll
     * @loadFixture admin
     * @loadFixture questions
     */
    public function exportXmlAction() {
        $this->reset();
        $this->_setUpAdminArea();
        $this->dispatch("productquestions_admin/adminhtml_index/exportXml/");
        $request = Mage::app()->getRequest();
        $this->assertEquals('exportXml', $request->getActionName());
    }

}
