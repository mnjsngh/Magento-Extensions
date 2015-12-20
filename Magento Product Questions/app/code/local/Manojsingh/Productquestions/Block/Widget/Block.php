<?php
class Manojsingh_Productquestions_Block_Widget_Block extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    public $collection = null;
    protected $urlType = 0;

    public function getDate($item)
    {
        $date = new  Zend_Date($item->getQuestionDate());
        return $date->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
    }

    public function getUrlQuestions($item)
    {
        $product = Mage::getModel('catalog/product')->load($item->getQuestionProductId());
        $productId = $product->getId();
        if ($this->getUrlType() == 1) {
            return $product->getProductUrl();
        }

        $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        if (Mage::getStoreConfig('productquestions/seo/enable_url_rewrites') && Mage::getModel('core/url_rewrite')->load($product->getId(), 'product_id')->getId()) {
            $productUrl = $product->getProductUrl();
            $fileExtentionPos = ($suffix == '') ? strlen($productUrl) : strrpos($productUrl, $suffix);
            $url = substr($productUrl, 0, $fileExtentionPos) . Manojsingh_Productquestions_Model_Urlrewrite::SEO_SUFFIX . $suffix;
        } else {
            $params = array('id' => $productId);
            $url = Mage::getUrl('productquestions/index/index/', $params);
        }

        return $url;
    }

    public function getQuestion($item)
    {
        $replace = array(' ' => '',
            '\n' => '');

        $text = $item->getQuestionText();

        if (strlen($text) > 0) {
            $text = trim(implode(' ', preg_split('/\s+/', strip_tags($text))));
            $words = explode(' ', $text);

            if (count($words) <= Manojsingh_Productquestions_Helper_Data::WORDS_IN_QUESTIONS) {
                return $text;
            }
            $resultWords = array();
            for ($i = 1; $i <= Manojsingh_Productquestions_Helper_Data::WORDS_IN_QUESTIONS; $i++) {
                $resultWords[] = $words[$i - 1];
            }
            $resultText = implode(' ', $resultWords);
            $resultText .= '...';
            return $resultText;
        }
        return $item->getQuestionText();
    }

    protected function _beforeToHtml()
    {
        $this->collection = Mage::getModel('productquestions/productquestions')->getCollection();
        $this->collection->addStoreFilter()
            ->addStoreFilter()
            ->addVisibilityFilter()
            ->addAnsweredFilter()
            ->applySorting(Manojsingh_Productquestions_Model_Source_Question_Sorting::BY_DATE, Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_DESC);

        if (!is_null($numQuestions = $this->getNumQuestions()))
            $this->collection->setPageSize($numQuestions);
        else $this->collection->setPageSize(5);
        $this->urlType = $this->getData('url_type');
        $this->setTemplate('productquestions/widget/block.phtml');

    }

}
