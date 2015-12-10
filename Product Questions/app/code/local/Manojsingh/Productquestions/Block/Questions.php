<?php
class Manojsingh_Productquestions_Block_Questions extends Mage_Core_Block_Template {
    /*
     * Internal Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection object
     */

    protected $_collection = null;

    /*
     * The quantity of last asked questions displayed
     */
    protected $_showLastX = false;

    /*
     * Pager block name in layout
     */
    protected $_pagerName = 'productquestions_pager';

    public function __construct() {
        parent::__construct();
        $this->setTemplate('productquestions/questions.phtml');
    }

    protected function _prepareCollection() {
        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        if (!($product instanceof Mage_Catalog_Model_Product))
            return false;

        $productId = $product->getId();

        $this->setProduct($product);

        $this->_collection = Mage::getResourceModel('productquestions/productquestions_collection')
                ->addProductFilter($productId)
                ->addVisibilityFilter()
                ->addAnsweredFilter()
                ->addStoreFilter()
                ->addLastXFilter($this->_showLastX);
        return $this;
    }

    protected function _prepareLayout() {
       // $this->getLayout()->getBlock('head')->addCss('css/productquestions.css');
        return parent::_prepareLayout();
    }

    protected function _toHtml() {
        if (Manojsingh_Productquestions_Helper_Data::isModuleOutputDisabled())
            return '';

        $storeId = Mage::app()->getStore()->getId();
        $route = $this->getRequest()->getModuleName();
        if ($route != 'productquestions')
            $this->_showLastX = (int) Mage::getStoreConfig('productquestions/interface/show_last_x', $storeId);

        $this->setShowPager('productquestions' == $this->getRequest()->getModuleName()
                || !($this->_showLastX));

        if (false === $this->_prepareCollection())
            return '';

        if ($sorter = $this->getLayout()->getBlock('productquestions_sorter')) {
            list($sortOrder, $sortDir) = $sorter->getCurrentSorting();
            if ($sortOrder)
                $this->_collection = $this->_collection->applySorting($sortOrder, $sortDir);
        }

        if ($this->getShowPager()
                && $pager = $this->getLayout()->getBlock($this->_pagerName)
        ) {
            $qid = $this->getQuestionId();
            if (null !== $qid
                    && $allIds = $this->_collection->getAllIdsFiltered()
            ) {
                $pos = array_search($qid, $allIds);

                if (false !== $pos
                        && ($pageSize = $pager->getLimit())
                        && $pageSize < count($allIds)
                        && ($pageNum = 1 + (int) ($pos / $pageSize))
                )
                    $this->getRequest()->setParam($pager->getPageVarName(), $pageNum);
            }
            $this->_collection = $pager
                    ->setCollection($this->_collection)
                    ->getCollection();
        }

        $this->setVotingAllowed(Mage::getStoreConfig('productquestions/interface/guests_allowed_to_vote', $storeId)
                || Mage::getSingleton('customer/session')->isLoggedIn());

        return parent::_toHtml();
    }

}
