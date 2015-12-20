<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Grid_Column_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        $value = sprintf('<a href="%s" target="_blank">%s</a>',  Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit',
            array('id' => $row->getData('question_product_id'))), $value);
        return $value;
    }
}
