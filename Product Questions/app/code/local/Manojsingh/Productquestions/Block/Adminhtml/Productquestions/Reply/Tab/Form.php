<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Reply_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $data = Mage::registry('productquestions_data');
        $data['question_datetime'] = $data['question_date'];

        $productName = isset($data['question_product_name']) ? $data['question_product_name'] : 'unknown';
        $productId = isset($data['question_product_id']) ? $data['question_product_id'] : 0;
        $storeId = isset($data['question_store_id']) ? $data['question_store_id'] : Mage::app()->getStore()->getId();

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('productquestions_form', array('legend' => $this->__('Question details')));

        $fieldset->addField('question_product_link', 'note', array(
            'label' => $this->__('Product'),
            'text' => '<a href="#" onclick="window.open(\'' . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array('id' => $productId)) . '\', \'' . $productName . '\', \'\').focus()" title="' . $this->__('Open product page in new window') . '">' . $productName . '</a>',
        ));

        $fieldset->addField('question_product_name', 'hidden', array(
            'name' => 'question_product_name',
        ));

        $fieldset->addField('question_product_id', 'hidden', array(
            'name' => 'question_product_id',
        ));

        $fieldset->addField('question_store_id', 'hidden', array(
            'name' => 'question_store_id',
        ));

        $fieldset->addField('asked_from', 'note', array(
            'label' => $this->__('Asked from'),
            'text' => Mage::getSingleton('adminhtml/system_store')->getStoreNameWithWebsite($storeId),
        ));

        $fieldset->addField('question_datetime', 'hidden', array(
            'name' => 'question_datetime',
        ));

        $fieldset->addField('question_date', 'date', array(
            'name' => 'question_date',
            'label' => $this->__('Asked on'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));

        if (Mage::app()->isSingleStoreMode())
            $fieldset->addField('question_store_ids', 'hidden', array(
                'name' => 'question_store_ids[]',
                'value' => Mage::app()->getStore()->getId(),
            ));
        else
            $fieldset->addField('question_store_ids', 'multiselect', array(
                'name' => 'question_store_ids[]',
                'label' => $this->__('Show in stores'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));

        $fieldset->addField('question_author_name', 'text', array(
            'name' => 'question_author_name',
            'label' => $this->__('Author name'),
            'required' => true,
            'style' => 'width:700px;',
        ));

        $fieldset->addField('question_author_email', 'text', array(
            'name' => 'question_author_email',
            'label' => $this->__('Author email'),
            'required' => true,
            'class' => 'validate-email',
            'style' => 'width:700px;',
        ));

        $fieldset->addField('question_text', 'editor', array(
            'name' => 'question_text',
            'label' => $this->__('Question'),
            'required' => true,
            'style' => 'width:700px; height:200px;',
        ));

        $fieldset->addField('question_reply_text', 'editor', array(
            'name' => 'question_reply_text',
            'label' => $this->__('Your reply'),
            'title' => $this->__('Your reply'),
            'style' => 'width:700px; height:500px;',
            'wysiwyg' => false,
            'required' => true,
        ));

        if ($data)
            $form->setValues($data);

        return parent::_prepareForm();
    }

}
