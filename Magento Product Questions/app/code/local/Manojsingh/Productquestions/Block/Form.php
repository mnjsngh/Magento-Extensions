<?php
class Manojsingh_Productquestions_Block_Form extends Mage_Core_Block_Template {
    /*
     * @var bool Indicates whether this form is situated inside parent PQ block
     */

    protected $_hasParent = false;

    public function __construct() {
        parent::__construct();

        //add antispam code value
        if (!$this->getAntiSpamCode()) {
            $antiSpamCode = rand();
            $this->setAntiSpamCode($antiSpamCode);
            Mage::getSingleton('core/session')->setManojsinghProductQuestionsAntiSpamCode(md5($antiSpamCode));
        }
        $this->setTemplate('productquestions/form.phtml');
    }

    protected function _prepareLayout() {
        $this->_hasParent = (bool) $this->getLayout()->getBlock('productquestions');

        if (!$this->_hasParent)
            //$this->getLayout()->getBlock('head')->addCss('css/productquestions.css');

        return parent::_prepareLayout();
    }

    /*
     * Returns POST action URL for the form
     * @return string Action URL
     */

    public function getAction() {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('productquestions/index/post', array('id' => $productId));
    }

    protected function _toHtml() {
        $storeId = Mage::app()->getStore()->getId();

        if (Manojsingh_Productquestions_Helper_Data::isModuleOutputDisabled($storeId))
            return '';
        if (!Manojsingh_Productquestions_Helper_Data::checkIfGuestsAllowed($storeId))
            return Mage::helper('productquestions')->getPleaseRegisterMessage($storeId);

        if (!$this->getQuestionAuthorName()
                || !$this->getQuestionAuthorEmail()
        ) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer && $customer->getId()) {
                if (!$this->getQuestionAuthorName()) {     // add logged in customer name and surname as nickname
                    //$this->setQuestionAuthorName(trim($customer->getFirstname() . ' ' . $customer->getLastname()));
                    $this->setQuestionAuthorName(trim(Mage::getSingleton('customer/session')->getCustomer()->getName()));
                }
                if (!$this->getQuestionAuthorEmail()) {    // add logged in customer email
                    $this->setQuestionAuthorEmail($customer->getEmail());
                }
            }
        }

        $product = Mage::helper('productquestions')->getCurrentProduct();
        if (!($product instanceof Mage_Catalog_Model_Product))
            return '';

        $this->setProduct($product);

        $data = Mage::getSingleton('core/session')->getProductquestionsData(true);

        if (is_array($data))
            $this->setData(array_merge($this->getData(), $data));

        return parent::_toHtml();
    }

    public function getAdvanceNewsletterSegments($id) {
        if (version_compare(Mage::helper('productquestions')->getAdvancedNewsletterVersion(), '2.0', '>=')) {
            $segments = Mage::getModel('advancednewsletter/segment')->getCollection()->addDefaultCategoryFilter($id)->getItems();
            $anlSegments = array();
            foreach ($segments as $segment) {
                $anlSegments[] = array(
                    'value' => $segment->getCode(),
                    'label' => $segment->getTitle(),
                );
            }
        } else {
            $anlSegments = Mage::getModel('advancednewsletter/segmentsmanagment')->getCategoryDefaultSegments($id);
        }
        return $anlSegments;
    }

    public function getCurrentCategoryId() {

        if (Mage::registry('current_category')) {
            return Mage::registry('current_category')->getId();
        }

        return null;
    }

}
