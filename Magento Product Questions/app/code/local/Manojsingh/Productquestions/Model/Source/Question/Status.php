<?php
class Manojsingh_Productquestions_Model_Source_Question_Status {
    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 2;

    public static function toShortOptionArray() {
        return array(
            self::STATUS_PUBLIC => Mage::helper('productquestions')->__('Public'),
            self::STATUS_PRIVATE => Mage::helper('productquestions')->__('Private')
        );
    }

    public static function toOptionArray() {
        $res = array();

        foreach (self::toShortOptionArray() as $key => $value)
            $res[] = array(
                'value' => $key,
                'label' => $value);

        return $res;
    }

}
