<?php
class Manojsingh_Productquestions_Block_Sorter extends Mage_Core_Block_Template {

    protected $_orderVarName = 'orderby';
    protected $_dirVarName = 'dir';

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('productquestions/sorter.phtml');
    }

    /*
     * Returns array of allowed question sorting
     * @return array Sorting_ley => sorting_field_name
     */

    public static function getAllowedSorting() {
        $allowedSorting = Mage::getStoreConfig('productquestions/interface/allowed_sorting_type');
        $allowedSorting = $allowedSorting ? explode(',', $allowedSorting) : array();

        $res = array();
        if (empty($allowedSorting))
            return $res;

        $allSortings = Manojsingh_Productquestions_Model_Source_Question_Sorting::toShortOptionArray();

        foreach ($allowedSorting as $key)
            if (array_key_exists($key, $allSortings))
                $res[$key] = $allSortings[$key];

        return $res;
    }

    /*
     * Returns current question sorting field and direction enclosed in array
     * @return array Sorting order (field) and direction
     */

    public function getCurrentSorting() {
        $allowedSorting = array_keys(self::getAllowedSorting());

        if (empty($allowedSorting))
            return array(false, false);

        $sortOrder = $this->getRequest()->getParam($this->_orderVarName);
        if (!$sortOrder
                || !in_array($sortOrder, $allowedSorting)
        )
            $sortOrder = reset($allowedSorting);

        $sortDir = $this->getRequest()->getParam($this->_dirVarName);
        if (Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_ASC != $sortDir
                && Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_DESC != $sortDir
        )
            $sortDir = Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_ASC;

        return array($sortOrder, $sortDir);
    }

    /*
     * Returns sorting order URL
     * @param int $sortOrder Sorting order index
     * @return string Sorting URL
     */

    public function getSortOrderUrl($sortOrder) {
        return $this->getSorterUrl(array($this->_orderVarName => $sortOrder));
    }

    /*
     * Returns inverted sorting direction
     * @param string $dir Current direction
     * @return string Inverted direction
     */

    public static function getInvertedDir($dir) {
        return (Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_ASC == $dir) ? Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_DESC : Manojsingh_Productquestions_Model_Source_Question_Sorting::SORT_ASC;
    }

    /*
     * Returns sorting direction URL
     * @param string $direction Sorting direction
     * @return string Direction URL
     */

    public function getSortDirUrl($direction) {
        return $this->getSorterUrl(array($this->_dirVarName => $direction));
    }

    /*
     * Returns URL with parameters encoded
     * @param none|array URL parameters
     * @return string URL
     */

    public function getSorterUrl($params=array()) {
        $urlParams = array();
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_query'] = $params;

        return $this->getUrl('*/*/*', $urlParams);
    }

}
