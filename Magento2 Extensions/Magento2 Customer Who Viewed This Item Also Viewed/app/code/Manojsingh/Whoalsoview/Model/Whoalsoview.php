<?php namespace Manojsingh\Whoalsoview\Model;
class Whoalsoview extends \Magento\Framework\Model\AbstractModel
{
	protected $_whoalsoview;
	
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
	
     protected function _construct()
    {
        $this->_init('Manojsingh\Whoalsoview\Model\ResourceModel\Whoalsoview');
    }

}
	 
