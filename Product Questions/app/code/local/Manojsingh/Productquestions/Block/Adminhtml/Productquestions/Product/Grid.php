<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setRowClickCallback('productquestions.gridRowClick');
        $this->setUseAjax(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('review')->__('ID'),
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('review')->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('type',
            array(
                'header' => Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));


        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header' => Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
            ));

        if ((int)$this->getRequest()->getParam('store', 0)) {
            $this->addColumn('custom_name', array(
                'header' => Mage::helper('review')->__('Name in Store'),
                'index' => 'custom_name'
            ));
        }

        $this->addColumn('sku', array(
            'header' => Mage::helper('review')->__('SKU'),
            'width' => '80px',
            'index' => 'sku'
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header' => Mage::helper('catalog')->__('Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('review')->__('Qty'),
            'width' => '130px',
            'type' => 'number',
            'index' => 'qty'
        ));

        $this->addColumn('visibility',
            array(
                'header' => Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type' => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
            ));

        $this->addColumn('status', array(
            'header' => Mage::helper('review')->__('Status'),
            'width' => '90px',
            'index' => 'status',
            'type' => 'options',
            'source' => 'catalog/product_status',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header' => Mage::helper('review')->__('Websites'),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/jsonProductInfo', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        return $this;
    }
}
