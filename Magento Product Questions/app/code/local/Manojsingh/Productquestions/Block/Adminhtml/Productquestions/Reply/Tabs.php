<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Reply_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('productquestions_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Question'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => $this->__('Details'),
            'title' => $this->__('Details'),
            'content' => $this->getLayout()->createBlock('productquestions/adminhtml_productquestions_reply_tab_form')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
