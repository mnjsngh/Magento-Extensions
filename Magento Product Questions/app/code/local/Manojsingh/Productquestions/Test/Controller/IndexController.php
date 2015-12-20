<?php
class Manojsingh_Productquestions_Test_Controller_IndexController extends EcomDev_PHPUnit_Test_Case_Controller {

    public function setUp() {
        //Manojsingh_Productquestions_Test_Mocks_Foreignresetter::dropForeignKeys();
        parent::setUp();
    }

    /**
     * @test
     * @loadFixture customer
     * @loadFixture questions
     * @dataProvider provider__vote
     * Checks voting for customer/guest, check voting for voted questions, check voting with incorrect data
     */
    public function voteAction($data) {
        $customerSession = Mage::getSingleton('customer/session');
        $customerSession->setVotedQuestions($data['voted']);
        $customerSession->loginById($data['customer_id']);
        Mage::app()->getStore(1)->setConfig('productquestions/interface/guests_allowed_to_vote', $data['guests']);
        $this->dispatch("productquestions/index/vote/id/{$data['qid']}/value/{$data['value']}/");
        $session = Mage::getSingleton('core/session');
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        try {
            $customerSession->logout();
        } catch (Exception $e) {
            
        }
        $this->assertEquals($data['expected'], $actual);
    }

    public function provider__vote() {
        return array(
            array(array('qid' => '2', 'value' => "1", 'guests' => '0', 'customer_id' => '1', 'voted' => '0,1', 'expected' => 'success: Your voice has been accepted. Thank you!')),
            array(array('qid' => '2', 'value' => "1", 'guests' => '1', 'customer_id' => '2', 'voted' => '0,1', 'expected' => 'success: Your voice has been accepted. Thank you!')),
            array(array('qid' => '1', 'value' => "0", 'guests' => '0', 'customer_id' => '0', 'voted' => '0,1', 'expected' => 'notice: Guests are not allowed to vote!')),
            array(array('qid' => '2', 'value' => "1", 'guests' => '1', 'customer_id' => '1', 'voted' => '0,1,2,3', 'expected' => 'notice: You have already voted on this question!')),
            array(array('qid' => '30', 'value' => "5", 'guests' => '1', 'customer_id' => '1', 'voted' => '0,1', 'expected' => 'error: Unable to vote. Please, try again later.')),
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @loadFixture product
     * @dataProvider provider__index
     */
    public function indexAction($data) {
        $this->dispatch("productquestions/index/index/id/{$data['product_id']}/category/2/");
        $request = Mage::app()->getRequest();
        $this->assertEquals($data['expected'], $request->getActionName());
        $session = Mage::getSingleton('core/session');
        if (!$data['product_id']) {
            $actual = $session->getMessages()->toString();
            $session->getMessages()->clear();
            $this->assertEquals('error: No product selected', $actual);
        }
        else
            $this->assertLayoutBlockRendered('productquestions');
    }

    public function provider__index() {
        return array(
            array(array('product_id' => '12', 'expected' => 'index')),
            array(array('product_id' => '', 'expected' => 'index'))
        );
    }

    /**
     * @test
     * @loadFixture questions
     * @loadFixture product
     * @dataProvider provider__post
     */
    public function postAction($data) {
        Mage::app()->getRequest()->setPost($data['data']);
        Mage::app()->getStore(1)->setConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_RECIPIENT, 'admin@admin.com');
        Mage::app()->getStore(1)->setConfig('productquestions/autorespond/status', '1');
        Mage::app()->getStore(1)->setConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_SENDER, 'auto@sender.com');
        Mage::getSingleton('core/session')->setManojsinghProductQuestionsAntiSpamCode('aaffcd98fff61439a3e7909cfd649649');
        $this->dispatch("productquestions/index/post/id/{$data['product_id']}");
        $request = Mage::app()->getRequest();
        $this->assertEquals('post', $request->getActionName());
        $session = Mage::getSingleton('core/session');
        $actual = $session->getMessages()->toString();
        $session->getMessages()->clear();
        $this->assertEquals($data['expected'], $actual);
    }

    public function provider__post() {
        return array(
            array(array('product_id' => '12', 'expected' => 'post', 'data' =>
                    array(
                        'question_antispam_code' => 'aaffcd98fff61439a3e7909cfd649649',
                        'product_id' => '12',
                        'question_author_name' => 'customer',
                        'question_author_email' => 'john@doe.com',
                        'question_status' => '1',
                        'question_text' => 'Question for product'),
                    'expected' => 'success: Your question has been accepted for moderationerror: An error occured, while sending a reply message to you.'
            )),
            array(array('product_id' => '', 'expected' => 'error: No product selected')),
            array(array('product_id' => '12', 'expected' => "error: Please specify valid email addresserror: Nickname can't be emptyerror: Question text can't be emptyerror: Antispam code is invalid. Please, check if JavaScript is enabled in your browser settings."))
        );
    }

}