<?php
class Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /*
     * @var array List of fields to sort by
     */

    protected $_sortingFields = array(
        Manojsingh_Productquestions_Model_Source_Question_Sorting::BY_DATE => 'main_table.question_date',
        Manojsingh_Productquestions_Model_Source_Question_Sorting::BY_HELPFULLNESS => 'helpfulness',
    );

    public function _construct() {
        parent::_construct();
        $this->_init('productquestions/productquestions');
    }

    /*
     * Initializes collection SELECT
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    protected function _initSelect() {
        parent::_initSelect();

        $this->getSelect()
                ->joinLeft(array('h' => $this->getTable('productquestions/helpfulness')), 'h.question_id=main_table.question_id', array('vote_count', 'vote_sum',
                    'vote_yes','vote_no','report','helpfulness' => new Zend_Db_Expr('vote_sum/if(vote_count=0,1,vote_count)*100')));

        return $this;
    }

    /*
     * Covers original bug in Varien_Data_Collection_Db
     */

    public function getSelectCountSql() {
        $this->_renderFilters();

        return $this->getConnection()
                        ->select()
                        ->from($this->getSelect(), 'COUNT(*)');
    }

    /*
     * Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
     */

    public function getAllIds() {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.' . $this->getResource()->getIdFieldName());

        return $this->getConnection()->fetchCol($idsSelect);
    }

    /*
     * Returns question IDs with filter applied
     * @return array
     */

    public function getAllIdsFiltered() {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->from(null, 'main_table.' . $this->getResource()->getIdFieldName());

        return $this->getConnection()->fetchCol($idsSelect);
    }

    /*
     * Applies product filter to the collection
     * @param int $productId
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function addProductFilter($productId) {
        $this->getSelect()->where('main_table.question_product_id=?', $productId);

        return $this;
    }

    /*
     * Applies store filter to the collection
     * @param
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function addStoreFilter($storeId = null) {
        if (is_null($storeId)) {
            if (Mage::app()->isSingleStoreMode())
                return $this;

            $storeId = Mage::app()->getStore()->getId();
        }
        $this->getSelect()->where('find_in_set(0, question_store_ids) OR find_in_set(?, question_store_ids)', (int) $storeId);

        return $this;
    }

    /*
     * Applies visibility filter to the collection
     * @param int $visibility
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function addVisibilityFilter($visibility = Manojsingh_Productquestions_Model_Source_Question_Status::STATUS_PUBLIC) {
        $this->getSelect()->where('main_table.question_status=?', $visibility);

        return $this;
    }

    /*
     * Restricts collection SELECT result set size
     * @param bool|int $lastX Quantity of questions returned
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function addLastXFilter($lastX = false) {
        if ($lastX)
            $this
                    ->setPageSize($lastX)
                    // ->setCurPage(0)
                    ->getSelect()->limit($lastX);

        return $this;
    }

    /*
     * Applies answered sign filter to the collection
     * @param bool $answered
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function addAnsweredFilter($answered = true) {
        if ($answered)
            $this->getSelect()->where('main_table.question_reply_text!=?', '');
        else
            $this->getSelect()->where('main_table.question_reply_text=?', '');

        return $this;
    }

    /*
     * Applies sorting to the collection
     * @param int $sortOrder Sorting order index
     * @param string $sortDir Sorting direction
     * @return Manojsingh_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */

    public function applySorting($sortOrder, $sortDir) {
        if ($sortOrder
                && array_key_exists($sortOrder, $this->_sortingFields)
        )
            $this->getSelect()
                    ->order($this->_sortingFields[$sortOrder] . ' ' . $sortDir);

        return $this;
    }

}
