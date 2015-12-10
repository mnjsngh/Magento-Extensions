<?php
class Manojsingh_Productquestions_Test_Model_Productquestions extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::getModel('productquestions/productquestions');
        $this->_sess = Mage::getSingleton('core/session');
        $this->_customersess = Mage::getSingleton('customer/session');
        $this->_adminUrl = Mage::getSingleton('adminhtml/url');
        $this->_resource = Mage::getModel('productquestions/productquestions')->getResource();
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function getProductId() {
        $this->_data->load(1);
        $this->assertEquals(12, $this->_data->getProductId());
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function getQuestionText() {
        $this->_data->load(1);
        $this->assertEquals('question1', $this->_data->getQuestionText());
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function getQuestionReplyText() {
        $this->_data->load(1);
        $this->assertEquals('reply', $this->_data->getQuestionReplyText());
    }

    /**
     * @test
     * @loadFixture questions
     */
    public function validateIncorrectSpamcode() {
        $this->_data->load(1);
        $this->_data->setData('question_antispam_code', 'wrongcode');
        $this->assertEquals(array('Antispam code is invalid. Please, check if JavaScript is enabled in your browser settings.'), $this->_data->validate());
    }

    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__validate
     */
    public function validate($data) {
        $this->_data->load($data['question_id']);
        $this->assertEquals($this->_data->validate(), array($data['result']));
    }

    public function provider__validate() {
        return array(
            array(array('question_id' => 2, 'result' => "Please specify valid email address")),
            array(array('question_id' => 3, 'result' => "Nickname can't be empty")),
            array(array('question_id' => 4, 'result' => "Question text can't be empty"))
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__vote
     */
    public function vote($data) {
        $this->_data->load($data['question_id']);
        $this->_data->vote($data['value']);
        $this->_data->vote($data['value']);
        $db = $this->_resource->getReadConnection();
        $tableName = $this->_resource->getTable('productquestions/helpfulness');
        $voted = $db->fetchAll(
                $db->select()
                        ->from($tableName)
                        ->where('question_id=?', $data['question_id'])
        );
        $this->assertEquals($voted['0']['vote_sum'], $data['result']);
    }

    public function provider__vote() {
        return array(
            array(array('question_id' => 1, 'value' => "", 'result' => '0')),
            array(array('question_id' => 2, 'value' => "0", 'result' => '0')),
            array(array('question_id' => 3, 'value' => "1", 'result' => '2')),
            array(array('question_id' => 4, 'value' => "3", 'result' => '2'))
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__isvoted
     */
    public function isVoted($data) {
        $this->_data->load($data['question_id']);
        $this->_customersess->setVotedQuestions($data['check']);
        $this->assertEquals($this->_data->IsVoted(), $data['result']);
    }

    public function provider__isvoted() {
        return array(
            array(array('question_id' => 1, 'check' => '0,1,2,3', 'result' => true)),
            array(array('question_id' => 2, 'check' => '2', 'result' => true)),
            array(array('question_id' => 3, 'check' => '1', 'result' => false)),
            array(array('question_id' => 4, 'check' => '1,2,3,5', 'result' => false)),
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__validate
     */
    public function getAdminUrl($data) {
        $this->_data->load($data['question_id']);
        $this->assertEquals($this->_data->getAdminUrl(), $this->_adminUrl->getUrl('productquestions_admin/adminhtml_index/reply', array('id' => $data['question_id'])));
    }

    //tests for resource model
    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__res
     */
    public function deleteByProductId($data) {
        $actual = $this->_resource->deleteByProductId($data['product_id'], $data['store_id']);
        $this->assertEquals($data['result'], $actual);
    }

    public function provider__res() {
        return array(
            array(array('product_id' => 12, 'store_id' => null, 'title' => 'Product', 'result' => 4, 'result2' => 2)),
            array(array('product_id' => 12, 'store_id' => 1, 'title' => null, 'result' => 2, 'result2' => 2)),
            array(array('product_id' => 3, 'store_id' => null, 'title' => 'Product', 'result' => 0, 'result2' => 0)),
            array(array('product_id' => 4, 'store_id' => 1, 'title' => null, 'result' => 0, 'result2' => 0))
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @dataProvider provider__res
     */
    public function setProductTitleById($data) {
        $actual = $this->_resource->setProductTitleById($data['product_id'], $data['title'], $data['store_id']);
        $this->assertEquals($data['result2'], $actual);
    }

}
