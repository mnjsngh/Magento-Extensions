<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $session = Mage::getSingleton('adminhtml/session');
        $this->setId('productquestionsGrid')
            ->setDefaultSort('question_date')
            ->setDefaultDir('DESC')
            ->setUseAjax(true)
            ->setSaveParametersInSession(true);
        if (!is_string($session->getFilter()))
            $this->setDefaultFilter(array('question_replied' => ($this->getRequest()->getParam('id') ? '' : '0')));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('productquestions/productquestions')->getCollection();
        $collection->getSelect()
            ->columns(array('question_replied' => new Zend_Db_Expr('if(LENGTH(question_reply_text)>0,1,0)')));

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $collection->addFieldToFilter('question_product_id', $id);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('question_date', array(
            'header' => $this->__('Date'),
            'index' => 'question_date',
            'type' => 'datetime',
            'align' => 'right',
            'width' => '150px',
        ));

        $this->addColumn('question_replied', array(
            'index' => 'question_replied',
            'header' => $this->__('Replied'),
            'type' => 'options',
            'options' => array(
                1 => $this->__('Yes'),
                0 => $this->__('No')
            ),
            'filter_condition_callback' => array($this, '_filterReplied'),
        ));

        $this->addColumn('question_author_name', array(
            'index' => 'question_author_name',
            'header' => $this->__('Author name'),
        ));

        $this->addColumn('question_author_email', array(
            'index' => 'question_author_email',
            'header' => $this->__('Email'),
        ));

        $this->addColumn('question_text', array(
            'index' => 'question_text',
            'header' => $this->__('Question text'),
            'width' => '250px',
        ));

        if(is_null($idProduct = $this->getRequest()->getParam('id'))){


        $this->addColumn('question_product_name', array(
            'index' => 'question_product_name',
            'header' => $this->__('Product title'),
            'width' => '250px',
            'renderer'=>'Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Grid_Column_Product'
        ));
    }
        $this->addColumn('rating', array(
            'index' => 'helpfulness',
            'header' => $this->__('Rating/Vote count'),
            'align' => 'center',
            'renderer' => 'Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Grid_Column_Rating',
            'filter_condition_callback' => array($this, '_filterRating'),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('asked_from', array(
                'type' => 'store',
                'header' => $this->__('Asked from'),
                'align' => 'left',
                'width' => '100px',
                'index' => 'question_store_id',
            ));
            /*
              $this->addColumn('display_on', array(
              'type'      => 'store',
              'header'    => $this->__('Show on'),
              'align'     => 'left',
              'width'     => '80px',
              'index'     => 'question_store_id',
              )); /* */
        }

        if(!is_null($idProduct = $this->getRequest()->getParam('id'))){
         $backUrl='product/'.$idProduct.'/';
        } else $backUrl='';

        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Reply/Edit'),
                    'url' => array('base' => '*/*/reply/'.$backUrl),
                    'field' => 'id'
                ),
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array('base' => '*/*/delete/'.$backUrl),
                    'field' => 'id',
                    'confirm' => Mage::helper('productquestions')->__('Are you sure?')
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => '`main_table`.`question_id`',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {

        if (!($this->getRequest()->getParam('id'))) {
            $this->setMassactionIdField('`main_table`.`question_id`');
            $this->getMassactionBlock()->setFormFieldName('productquestions');

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => $this->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('ret' => $this->getRequest()->getActionName())),
                'confirm' => $this->__('Are you sure?')
            ));

            $statuses = Manojsingh_Productquestions_Model_Source_Question_Status::toOptionArray();

            array_unshift($statuses, array('label' => '', 'value' => ''));
            $this->getMassactionBlock()->addItem('question_status', array(
                'label' => $this->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true, 'ret' => $this->getRequest()->getActionName())),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'question_status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => $this->__('Status'),
                        'values' => $statuses
                    )
                )
            ));
            return $this;
        }
    }

    public function getRowUrl($row)
    {
        $param = array('id' => $row->getId());
        if ($id = $this->getRequest()->getParam('id')) {
            $param['product'] = $id;
        }
        return $this->getUrl(Mage::app()->getConfig()->getNode('admin/routers/productquestions_admin/args/frontName') . '/adminhtml_index/reply', $param);
    }

    protected function _filterReplied($collection, $column)
    {
        return $collection->getSelect()->having('question_replied=?', $column->getFilter()->getValue());
    }

    protected function _filterRating($collection, $column)
    {
        return $collection->getSelect()->having('helpfulness=?', $column->getFilter()->getValue());
    }

}
