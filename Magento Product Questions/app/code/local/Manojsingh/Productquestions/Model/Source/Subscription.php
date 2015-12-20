<?php
class Manojsingh_Productquestions_Model_Source_Subscription {
    const NONE = 1;
    const STANDARD = 2;
    const USING_ANL = 3;

    public static function toOptionArray() {
        $helper = Mage::helper('productquestions');
        return array(
            self::NONE => $helper->__("Don't display subscription"),
            self::STANDARD => $helper->__('Standard newsletter'),
            self::USING_ANL => $helper->__('Advanced newsletter'),
        );
    }

}
