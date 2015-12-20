<?php
class Manojsingh_Productquestions_Block_Summary extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('productquestions/summary.phtml');
    }

    protected function _toHtml() {
        if (Manojsingh_Productquestions_Helper_Data::isModuleOutputDisabled())
            return '';

        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        if (!($product instanceof Mage_Catalog_Model_Product))
            return '';

        $productId = $product->getId();

        $category = Mage::registry('current_category');
        if ($category instanceof Mage_Catalog_Model_Category)
            $categoryId = $category->getId();
        else
            $categoryId = false;

        $questionCount = Mage::getResourceModel('productquestions/productquestions_collection')
                ->addProductFilter($productId)
                ->addVisibilityFilter()
                ->addAnsweredFilter()
                ->addStoreFilter()
                ->getSize();

        $params = array('id' => $productId);
        if ($categoryId)
            $params['category'] = $categoryId;

        $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');

        /* if($urlKey = $product->getUrlKey())
          {
          $requestString = ltrim(Mage::app()->getFrontController()->getRequest()->getRequestString(), '/');

          $pqSuffix = $urlKey.$suffix;
          if($pqSuffix == substr($requestString, strlen($requestString)-strlen($pqSuffix)))
          {
          $requestString = substr($requestString, 0, strlen($requestString)-strlen($suffix));
          $this->setQuestionsPageUrl($this->getBaseUrl().$requestString.Manojsingh_Productquestions_Model_Urlrewrite::SEO_SUFFIX.$suffix);
          }
          } */

        if (Mage::getStoreConfig('productquestions/seo/enable_url_rewrites') && Mage::getModel('core/url_rewrite')->load($product->getId(), 'product_id')->getId()) {
            $productUrl = $product->getProductUrl();
            $fileExtentionPos = ($suffix == '') ? strlen($productUrl) : strrpos($productUrl, $suffix);
            $this->setQuestionsPageUrl(substr($productUrl, 0, $fileExtentionPos) . Manojsingh_Productquestions_Model_Urlrewrite::SEO_SUFFIX . $suffix);
        } else {
            $this->setQuestionsPageUrl(Mage::getUrl('productquestions/index/index/', $params));
        }

        $this->setQuestionCount($questionCount);

        return parent::_toHtml();
    }

}
