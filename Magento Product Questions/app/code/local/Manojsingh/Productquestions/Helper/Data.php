<?php
class Manojsingh_Productquestions_Helper_Data extends Mage_Core_Helper_Abstract {
    /*
     * Returns current product
     * @param bool $inspectRegistry Whether to check Magento registry for current product
     * @return Mage_Catalog_Model_Product Current product
     */
const WORDS_IN_QUESTIONS=7;

    public function getCurrentProduct($inspectRegistry = false) {
        if ($inspectRegistry) {
            $product = Mage::registry('product');
            if (!($product instanceof Mage_Catalog_Model_Product))
                $product = Mage::registry('current_product');

            if ($product instanceof Mage_Catalog_Model_Product)
                return $product;
        }

        $productId = (int) Mage::app()->getRequest()->getParam('id');
        if (!$productId)
            return $this->__('No product ID');

        $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

        if (!$product
                || !($product instanceof Mage_Catalog_Model_Product)
                || $productId != $product->getId()
                || !Mage::helper('catalog/product')->canShow($product)
                || !in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())
        )
            return $this->__('No such product');

        return $product;
    }

    /*
     * Checks whether module output is disabled in admin section
     * @return bool Check result
     */

    public static function isModuleOutputDisabled() {
        return (bool) Mage::getStoreConfig('advanced/modules_disable_output/Manojsingh_Productquestions');
    }

    /*
     * Returns service message telling the customer that his/her registration is needed
     * @return string Message itself
     */

    public function getPleaseRegisterMessage() {
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        return str_replace('href="/customer', 'href="' . $baseUrl . 'customer', Mage::getStoreConfig('productquestions/question_form/please_register'));
    }

    /*
     * Returns HTML containing links to product question page
     * @result string HTML of the block
     */

    public function getSummaryHtml() {
        return Mage::app()->getLayout()->createBlock('productquestions/summary')->toHtml();
    }

    /*
     * Checks whether current customer needs registration
     * @return bool Check result
     */

    public static function checkIfGuestsAllowed() {
        return Mage::getStoreConfig('productquestions/question_form/guests_allowed')
                || Mage::getSingleton('customer/session')->isLoggedIn();
    }


    public static function isAdvancedNewsletterInstalled() {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();

        return array_key_exists('Manojsingh_Advancednewsletter', $modules)
                && 'true' == (string) $modules['Manojsingh_Advancednewsletter']->active;
    }

    /*
     * Returns current version of the Advanced Newsletter extension
     * @return int Version number
     */

    public static function getAdvancedNewsletterVersion() {
        if (!$anVersion = Mage::getConfig()->getModuleConfig('Manojsingh_Advancednewsletter')->version)
            return false;

        $parts = explode('.', $anVersion);
        while (count($parts) < 3)
            $parts[] = 0;
        $ver = 0;
        foreach ($parts as $p)
            $ver = $ver * 100 + $p;

        return $ver;
    }

    /*
     * Subscribes customer to Advanced Newsletter segments
     * @param string $email Customer email
     * @param string $name Customer name
     * @param array $segments Advanced Newsletter segments
     * @return null
     */

    public function subscribeAdvancedNewsletterSegment($email, $name, $segments) {
        if (!is_array($segments))
            $segments = array($segments);

        $anVersion = self::getAdvancedNewsletterVersion();

        $anModel = Mage::getModel('advancednewsletter/subscriptions');
        $session = Mage::getSingleton('core/session');
        try {
            switch ($anVersion) {
                case ($anVersion < 10200):
                    foreach ($segments as $segment)
                        $anModel->subscribe(// public function subscribe($email, $firstname, $lastname, $segment)
                                $email, '', // $firstname
                                $name, // $lastname
                                $segment);
                    break;
                case ($anVersion >= 20000):
                    $apiModel = Mage::getModel('advancednewsletter/api');
                    $apiModel->subscribe($email, $segments, array('last_name' => $name));
                    break;
                default:
                    foreach ($segments as $segment)
                        $anModel->subscribe(// public function subscribe($email, $firstname, $lastname, $salutation, $phone, $segment)
                                $email, '', // $firstname
                                $name, // $lastname
                                null, // $salutation,
                                null, // $phone,
                                $segment);
                    break;
            }
        } catch (Exception $e) {
            $session->addException($e, $this->__($e->getMessage()));
        }
        /*
          if($anVersion < 10200) // 1.0 & 1.0.2
          foreach($segments as $segment)
          $anModel->subscribe( // public function subscribe($email, $firstname, $lastname, $segment)
          $email,
          '',     // $firstname
          $name,  // $lastname
          $segment);
          else  // 1.2.0 and above
          foreach($segments as $segment)
          $anModel->subscribe( // public function subscribe($email, $firstname, $lastname, $salutation, $phone, $segment)
          $email,
          '',     // $firstname
          $name,  // $lastname
          null,   // $salutation,
          null,   // $phone,
          $segment);
         */
    }

    /*
     * Subscribes customer to newsletter
     * @param string $email Customer email
     * @return null
     */

    public function subscribeCustomer($email) {
        $subscriber = Mage::getModel('newsletter/subscriber');
        $session = Mage::getSingleton('core/session');
        //validate email before subscribe
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $session->addError($this->__('Please, enter correct email address'));
        else
            try {
                $subscriber->subscribe($email);
                if ($subscriber->getIsStatusChanged())
                    $session->addSuccess($this->__('You have been subscribed to newsletters'));
            } catch (Exception $e) {
                $session->addException($e, $this->__('There was a problem with the newsletter subscription')
                        . ($e instanceof Mage_Core_Exception) ? ': ' . $e->getMessage() : '');
            }
    }

    /*
     * Replaces URLs found in text with the appropriate links
     * @param string $text A text to parse
     * @return string Processed text
     */

    public static function parseURLsIntoLinks($text) {
        if (!Mage::getStoreConfig('productquestions/interface/parse_urls_into_links'))
            return nl2br(htmlentities($text, null, 'UTF-8'));

        $parts = preg_split('#((?:https?|ftp)://\S+)#', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $isHref = true;
        $res = '';
        foreach ($parts as $part)
            $res .= ($isHref = !$isHref) ? '<a href="' . $part . '">' . $part . '</a>' : nl2br(htmlentities($part, null, 'UTF-8'));
        return $res;
    }

    public function getSender($storeId = null) {

        $senderCode = Mage::getStoreConfig('productquestions/email/sender_email_identity', $storeId);
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_' . $senderCode . '/name', $storeId),
            'mail' => Mage::getStoreConfig('trans_email/ident_' . $senderCode . '/email', $storeId),
        );

        return $sender;
    }

    public function getStoreName() {
        $store = Mage::app()->getStore();
        $name = Mage::getStoreConfig('general/store_information/name', $store->getId());
        return ($name) ? $name : $store->getName();
    }

    public function getSEOurlRewrite() {
        return Mage::getStoreConfigFlag('productquestions/seo/enable_url_rewrites');
    }

    public function getQuestionsLink($productId, $url) {

        if ($this->getSEOurlRewrite() && Mage::getModel('core/url_rewrite')->load($productId, 'product_id')->getId()) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
            $productUrl = $product->getProductUrl();
            $fileExtentionPos = ($suffix == '') ? strlen($productUrl) : strrpos($productUrl, $suffix);
            $url = substr($productUrl, 0, $fileExtentionPos) . Manojsingh_Productquestions_Model_Urlrewrite::SEO_SUFFIX . $suffix;
        }
        return $url;
    }

    public function getCustomerId() {
        $customerId = (int) Mage::getSingleton('customer/session')->getCustomerId();

        return $customerId;
    }

}
