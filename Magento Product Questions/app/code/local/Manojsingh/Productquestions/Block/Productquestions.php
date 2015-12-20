<?php
class Manojsingh_Productquestions_Block_Productquestions extends Manojsingh_Productquestions_Block_Questions {

    protected function _toHtml() {
        $this->_pagerName = 'productquestions_list.toolbar';
        return parent::_toHtml();
    }

}
