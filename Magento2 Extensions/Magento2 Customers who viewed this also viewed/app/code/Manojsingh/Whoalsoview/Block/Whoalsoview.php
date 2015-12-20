<?php
namespace Manojsingh\Whoalsoview\Block;
use Magento\Store\Model\ScopeInterface;
class Whoalsoview extends \Magento\Catalog\Block\Product\AbstractProduct
{

    protected $_viewCollectionFactory;
    
    protected $_session;
    
    protected $_storeManager;
    
    protected $_coreRegistry;
    
    protected $_productcollection;
    
    protected $_catalogProductVisibility;
    
    protected $_urlHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollection,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Manojsingh\Whoalsoview\Model\ResourceModel\Whoalsoview\CollectionFactory $viewCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_session = $session;
        $this->_viewCollectionFactory = $viewCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_productcollection = $productcollection;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_urlHelper = $urlHelper;
    }
    
    public function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product;
    }
    
    public function getBaseurl()
    {
		$site_url = $this->_storeManager->getStore()->getBaseUrl();
		return $site_url;
	}

    public function lastviewproduct($pro_sku,$cat_id)
    {
		$max = $this->getMaxProductDisplay();
        $product_sku_count = array();
        if(!($this->isEnabled())) {
            return $product_sku_count;
        }
        $Session = $this->_session;
        $user_session = $Session->getMosViewUser();
        $connection = $this->_viewCollectionFactory->create()
        ->addFilter('product_sku', $pro_sku)
        ->addFieldToFilter('session_cod',array('neq'=>$user_session)); 
        
        $all_data = $connection->getData();
        
        $product_session = array();
        foreach($all_data as $data)
        {
            $connection_2 = $this->_viewCollectionFactory->create()
            ->addFilter('session_cod', $data['session_cod'])
            ->addFieldToFilter('product_sku',array('neq'=>$pro_sku)); 

            array_push($product_session, $connection_2->getData());
        } 
       
        $product_sku_array = array();
        foreach($product_session as $key => $prodct_data){
            foreach($prodct_data as $step_prodct){
                $cat_data_id =  explode(',',$step_prodct['product_categories']); 
                $result = array_intersect($cat_id, $cat_data_id);
                if($this->getshowCatProductOnly()){ 
					if(!empty($result)){ array_push($product_sku_array, $step_prodct['product_id']); }
                }
                else {
                   array_push($product_sku_array, $step_prodct['product_id']);
                }
            }
        }
        
        $product_sku_array_mini = $product_sku_array;

        foreach($product_sku_array_mini as $key=>$procuts_array)
        {
                $tmp = array_count_values($product_sku_array);
                $cnt = $tmp[$procuts_array]; 
                $product_sku_count[$procuts_array]=$cnt;
        }
        arsort($product_sku_count);

        foreach($product_sku_count as $key =>$pro_value)
		{
			$productIds[] = $key;
		}
		
		if(count($productIds))
		{
			if($this->getshowInStockProduct()){
				$productCollection = $this->_productcollection->create()
				->addAttributeToFilter('entity_id', array('in' => $productIds))
				->addAttributeToFilter('status', '1')
				->setPageSize($max)
                ->setCurPage(1);
			}else{
				$productCollection = $this->_productcollection->create()
				->addAttributeToFilter('entity_id', array('in' => $productIds))
				->setPageSize($max)
                ->setCurPage(1);
			}
			
			$productCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
			$productCollection = $this->_addProductAttributesAndPrices($productCollection);
			
		}else{
			$productCollection = array();
		}
		
        return $productCollection;
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    $this->_urlHelper->getEncodedUrl($url),
            ]
        ];
    }
	
	public function isEnabled($store = null)
    {		
        return (bool) $this->_scopeConfig->getValue('whoalsoview/general/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    public function getDisplayTitle($store = null)
    {
		$title = $this->_scopeConfig->getValue('whoalsoview/general/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $title;
    }
    
    public function getMaxProductDisplay($store = null)
    {
		$max_product_count = $this->_scopeConfig->getValue('whoalsoview/general/max_product_count', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);	
        return $max_product_count;
    }

    public function getshowInStockProduct($store = null)
    {	$show_in_stock_products = $this->_scopeConfig->getValue('whoalsoview/general/show_in_stock_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $show_in_stock_products;
    }
    
    public function getshowCatProductOnly($store = null)
    {
        $show_only_categories_products = $this->_scopeConfig->getValue('whoalsoview/general/show_only_categories_products', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return $show_only_categories_products;
    }

}
