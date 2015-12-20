<?php namespace Manojsingh\Whoalsoview\Model\ResourceModel\Whoalsoview;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Manojsingh\Whoalsoview\Model\Whoalsoview', 'Manojsingh\Whoalsoview\Model\ResourceModel\Whoalsoview');
    }
}
