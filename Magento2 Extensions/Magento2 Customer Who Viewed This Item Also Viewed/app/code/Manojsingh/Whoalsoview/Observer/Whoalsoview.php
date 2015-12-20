<?php
namespace Manojsingh\Whoalsoview\Observer;

use Magento\Framework\Event\ObserverInterface;

class Whoalsoview implements ObserverInterface
{
    protected $_session; 
     
    protected $_registry;
    
    protected $_backendAuthSession;
    
    protected $_modelFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Manojsingh\Whoalsoview\Model\Whoalsoview $modelFactory
    ) {
        $this->_registry = $registry;
        $this->_session = $session;
        $this->_modelFactory = $modelFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $Session = $this->_session;
        $getSession = $Session->getMosViewUser();
        if(!$getSession || $getSession=='')
           { 
              $cus_session = $this->generateRandomString();
              $Session->setMosViewUser($cus_session);
              $getSession = $cus_session;   
           }
        
        $id = $observer->getEvent()->getProduct()->getId();
        $product = $observer->getEvent()->getProduct();
        $name = $product->getName();
        $cat_id = $product->getCategoryIds();  
        $cur_category_id = implode(",",$cat_id);                       
        $sku = $product->getSku();
        $ip = $_SERVER['REMOTE_ADDR'];
        $model = $this->_modelFactory;
				if(!$Session->getData($getSession)){
                    $user_product_view_array = array();
                    array_push($user_product_view_array, $id);
                    $Session->setData($getSession,$user_product_view_array);
                    $model->setSessionCod($getSession);
                    $model->setProductId($id); 
                    $model->setProductSku($sku); 
                    $model->setProductCategories($cur_category_id);                                    
                    $model->setIp($ip);
                    $model->save();

                }else{
                    if(!in_array($id,$Session->getData($getSession)))
                    {
					   $sess = $Session->getData($getSession);
                       array_push($sess, $id);   
                       $Session->setData($getSession,$sess);
                       $model->setSessionCod($getSession);
                       $model->setProductId($id); 
                       $model->setProductSku($sku);
                       $model->setProductCategories($cur_category_id);     
                       $model->setIp($ip);
                       $model->save();                                       
                    }                    
                                    
                }
       
    }
    
    public function generateRandomString($length = 5) {
         $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
         $randomString = '';
         for ($i = 0; $i < $length; $i++) {
              $randomString .= $characters[rand(0, strlen($characters) - 1)];
         }
         return $randomString.time();
    }
}
