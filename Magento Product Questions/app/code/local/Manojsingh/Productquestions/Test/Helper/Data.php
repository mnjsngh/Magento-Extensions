<?php
class Manojsingh_Productquestions_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case {

    public function setUp() {
        parent::setUp();
        $this->_data = Mage::helper('productquestions');
    }

    /**
     * @test
     *
     */
    public function isModuleOutputDisabled() {
        Mage::app()->getStore()->setConfig('advanced/modules_disable_output/Manojsingh_Productquestions', true);
        $this->assertEquals(true, $this->_data->isModuleOutputDisabled());
        Mage::app()->getStore()->setConfig('advanced/modules_disable_output/Manojsingh_Productquestions', false);
        $this->assertEquals(false, $this->_data->isModuleOutputDisabled());
    }

    /**
     * @test
     */
    public function getPleaseRegisterMessage() {
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $check = str_replace('href="/customer', 'href="' . $baseUrl . 'customer', Mage::getStoreConfig('productquestions/question_form/please_register'));
        $this->assertEquals($check, $this->_data->getPleaseRegisterMessage());
    }

    /**
     * @test
     */
    public function getSummaryHtml() {
        $check = Mage::app()->getLayout()->createBlock('productquestions/summary')->toHtml();
        $this->assertEquals($check, $this->_data->getSummaryHtml());
    }

    /**
     * @test
     */
    public function checkIfGuestsAllowed() {
        Mage::app()->getStore()->setConfig('productquestions/question_form/guests_allowed', 1);
        $check = (bool) Mage::getStoreConfig('productquestions/question_form/guests_allowed');
        $this->assertEquals($check, $this->_data->checkIfGuestsAllowed());
        Mage::app()->getStore()->setConfig('productquestions/question_form/guests_allowed', 0);
        $check = (bool) Mage::getStoreConfig('productquestions/question_form/guests_allowed');
        $this->assertEquals($check, $this->_data->checkIfGuestsAllowed());
    }

    /**
     * @test
     */
    public function isAdvancedNewsletterInstalled() {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        $check = array_key_exists('Manojsingh_Advancednewsletter', $modules)
                && 'true' == (string) $modules['Manojsingh_Advancednewsletter']->active;
        $this->assertEquals($check, $this->_data->isAdvancedNewsletterInstalled());
    }

    /**
     * @test
     */
    public function getAdvancedNewsletterVersion() {
        if (!$anVersion = Mage::getConfig()->getModuleConfig('Manojsingh_Advancednewsletter')->version)
            $ver = false;
        else {
            $parts = explode('.', $anVersion);
            while (count($parts) < 3)
                $parts[] = 0;
            $ver = 0;
            foreach ($parts as $p)
                $ver = $ver * 100 + $p;
        }
        $this->assertEquals($ver, $this->_data->getAdvancedNewsletterVersion());
    }

    /**
     * @test
     * 
     * @dataProvider provider_subscribeANSegments
     */
    public function subscribeAdvancedNewsletterSegment($data) {
        if ($this->_data->isAdvancedNewsletterInstalled()) {
            $session = Mage::getSingleton('core/session');
            $this->_data->subscribeAdvancedNewsletterSegment($data['email'], $data['name'], $data['segments']);
            $actual = $session->getMessages()->toString();
            $session->getMessages()->clear();
            $this->assertEquals($data['expected'], $actual);
        }
    }

    public function provider_subscribeANSegments() {
        return array(
            array(array('segments' => array('0' => 'segment1'), 'name' => 'subscriber', 'email' => 'customer@customer1.com', 'expected' => '')),
            array(array('segments' => 'segment1', 'name' => 'subscriber', 'email' => 'customer@customer2.com', 'expected' => '')),
            array(array('segments' => '', 'name' => 'subscriber', 'email' => 'customer@customer1.com', 'expected' => '')),
            array(array('segments' => 'segment1', 'name' => 'subscriber', 'email' => 'customer@@customer1.com', 'expected' => 'error: Invalid email'))
        );
    }

    /**
     * @test
     * @dataProvider provider__subscribe
     */
    public function subscribeCustomer($data) {
        $this->_data->subscribeCustomer($data['email']);
        $sess = Mage::getSingleton('core/session');
        $actual = $sess->getMessages()->toString();
        $sess->getMessages()->clear();
        $this->assertEquals($data['message'], $actual);

        //$this->assertEquals($data['error'],$session->getException());
    }

    public function provider__subscribe() {
        return array(
            array(array('email' => 'customer@customer.com', 'message' => "success: You have been subscribed to newsletters")),
            array(array('email' => '', 'message' => 'error: Please, enter correct email address')),
            array(array('email' => 'customer@@customer.com', 'message' => 'error: Please, enter correct email address'))
        );
    }

    /**
     * @test
     * @dataProvider provider__parseurls
     */
    public function parseURLsIntoLinks($data) {
        Mage::app()->getStore()->setConfig('productquestions/interface/parse_urls_into_links', $data['allowed']);
        $this->assertEquals($data['result'], $this->_data->parseURLsIntoLinks($data['text']));
    }

    public function provider__parseurls() {
        return array(
            array(array('allowed' => '0', 'text' => 'text http://ya.ru text <br> text', 'result' => 'text http://ya.ru text &lt;br&gt; text')),
            array(array('allowed' => '1', 'text' => 'text http://ya.ru text <br> text', 'result' => 'text <a href="http://ya.ru">http://ya.ru</a> text &lt;br&gt; text')),
            array(array('allowed' => '1', 'text' => 'text https://www.ya.ru text <br> text <script>alert("hi");</script> text', 'result' => 'text <a href="https://www.ya.ru">https://www.ya.ru</a> text &lt;br&gt; text &lt;script&gt;alert("hi");&lt;/script&gt; text'))
        );
    }

    /**
     * @test
     * @dataProvider provider__getsender
     */
    public function getSender($data) {
        $senderCode = Mage::getStoreConfig('productquestions/email/sender_email_identity', $data['storeid']);
        Mage::app()->getStore($data['storeid'])->setConfig('trans_email/ident_' . $senderCode . '/name', $data['name']);
        Mage::app()->getStore($data['storeid'])->setConfig('trans_email/ident_' . $senderCode . '/email', $data['mail']);
        $sender = array(
            'name' => $data['name'],
            'mail' => $data['mail']
        );
        $this->assertEquals($sender, $this->_data->getSender($data['storeid']));
    }

    public function provider__getsender() {
        return array(
            array(array('storeid' => '0', 'name' => 'name', 'mail' => 'email@email.com')),
            array(array('storeid' => '1', 'name' => '', 'mail' => 'email@email.com')),
            array(array('storeid' => '', 'name' => 'name', 'mail' => 'email@email.com'))
        );
    }

    /**
     * @test
     *
     */
    public function getStoreName() {
        $store = Mage::app()->getStore();
        $name = Mage::getStoreConfig('general/store_information/name', $store->getId());
        $check = ($name) ? $name : $store->getName();
        $this->assertEquals($check, $this->_data->getStoreName());
    }

    /**
     * @test
     *
     */
    public function getSEOurlRewrite() {
        Mage::app()->getStore()->setConfig('productquestions/seo/enable_url_rewrites', 0);
        $this->assertEquals(false, $this->_data->getSEOurlRewrite());
        Mage::app()->getStore()->setConfig('productquestions/seo/enable_url_rewrites', 1);
        $this->assertEquals(true, $this->_data->getSEOurlRewrite());
    }

    /**
     * @test
     * @loadFixture product
     * @dataProvider provider__getquestionslink
     */
    public function getQuestionsLink($data) {

        Mage::app()->getStore()->setConfig('productquestions/seo/enable_url_rewrites', $data['seo']);
        $url = $data['url'];
        if (Mage::getModel('core/url_rewrite')->load($data['product_id'], 'product_id')->getId()) {
            $product = Mage::getModel('catalog/product')->load($data['product_id']);
            $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
            $productUrl = $product->getProductUrl();
            $fileExtentionPos = ($suffix == '') ? strlen($productUrl) : strrpos($productUrl, $suffix);
            $url = substr($productUrl, 0, $fileExtentionPos) . Manojsingh_Productquestions_Model_Urlrewrite::SEO_SUFFIX . $suffix;
        }
        $this->assertEquals($url, $this->_data->getQuestionsLink($data['product_id'], $data['url']));
    }

    public function provider__getquestionslink() {
        return array(
            array(array('seo' => '0', 'product_id' => '12', 'url' => 'http://site.com/')),
            array(array('seo' => '1', 'product_id' => '12', 'url' => 'http://site.com/')),
            array(array('seo' => '1', 'product_id' => '', 'url' => 'http://site.com/'))
        );
    }

}
