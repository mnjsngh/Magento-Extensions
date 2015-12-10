<?php
class Manojsingh_Productquestions_Model_Source_Question_Sorting {
    const BY_DATE = 1;
    const BY_HELPFULLNESS = 2;

    const SORT_ASC = 'ASC';
    const SORT_DESC = 'DESC';

    public static function toOptionArray() {
        $res = array();

        foreach (self::toShortOptionArray() as $key => $value)
            $res[] = array('value' => $key, 'label' => $value);

        return $res;
    }

    /*
     * Returns options array for sorting fields
     * @return array sorting_type => sorting_title
     */

    public static function toShortOptionArray() {
        return array(
            self::BY_DATE => Mage::helper('productquestions')->__('Date'),
            self::BY_HELPFULLNESS => Mage::helper('productquestions')->__('Helpfulness'),
        );
    }

    /*
     * Returns title of current sorting direction
     * @param int $dir Sorting direction
     * @return string Name of the direction
     */

    public static function getSortDirDescription($dir) {
        $helper = Mage::helper('productquestions');
        switch ($dir) {
            case self::SORT_ASC: return $helper->__('Ascending');
                break;
            case self::SORT_DESC: return $helper->__('Descending');
                break;
        }
        return 'Unknown';
    }

}
