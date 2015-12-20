<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Reply extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_mode = 'reply';
        $this->_objectId = 'id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_productquestions';

        $this->_updateButton('save', 'label', $this->__('Save'));
        $this->_updateButton('delete', 'label', $this->__('Delete'));

        $this->updateBackButtonUrl();
        $this->updateSaveButtonUrl();
        $this->updateDeleteButtonUrl();
        $this->_addButton('saveandcontinue', array(
            'label' => $this->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_addButton('saveandemail', array(
            'label' => $this->__('Save And Send Email'),
            'onclick' => 'saveAndEmail()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('productquestions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'productquestions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'productquestions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/1/" . (!is_null($idProduct = $this->getRequest()->getParam('product')) ? 'product/' . $idProduct . '/' : '') . "');
            }

            function saveAndEmail(){
                editForm.submit($('edit_form').action+'sendEmail/1/" . (!is_null($idProduct = $this->getRequest()->getParam('product')) ? 'product/' . $idProduct . '/' : '') . "');
            }
        ";
    }

    private function updateBackButtonUrl()
    {
        if (!is_null($idProduct = $this->getRequest()->getParam('product'))) {
            $this->_updateButton('back', 'onclick', "setLocation('" . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit',
                array('id' => $idProduct, 'tab' => 'product_info_tabs_Productquestions_product_tab')) . "');");
        }
    }

    private function updateSaveButtonUrl()
    {
        if (!is_null($idProduct = $this->getRequest()->getParam('product'))) {
            $this->_updateButton('save', 'onclick', "editForm.submit($('edit_form').action+'product/" . $idProduct . "/')");
        }
    }


    public function updateDeleteButtonUrl()
    {

        if (!is_null($idProduct = $this->getRequest()->getParam('product'))) {
            $param = array($this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'product' => $idProduct);
            $this->_updateButton('delete', 'onclick', "deleteConfirm('" . Mage::helper('adminhtml')->__('Are you sure you want to do this?') . "','" .
                $this->getUrl('*/*/delete', $param) . "');");
        }
    }

    public function getHeaderText()
    {
        $data = Mage::registry('productquestions_data');
        if (!empty($data))
            return htmlspecialchars($this->__('Reply question #%d from %s <%s>', $data['question_id'], $data['question_author_name'], $data['question_author_email']));
        else
            return $this->__('Question');
    }

}
