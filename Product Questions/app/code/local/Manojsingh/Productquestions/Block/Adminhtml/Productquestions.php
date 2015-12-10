<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        parent::__construct();

        $this->_controller = 'adminhtml_productquestions';
        $this->_blockGroup = 'productquestions';

        $this->_headerText = $this->__('Product questions');
        $this->_updateButton('add', 'label', $this->__('Add New Questions'));
    }

}
