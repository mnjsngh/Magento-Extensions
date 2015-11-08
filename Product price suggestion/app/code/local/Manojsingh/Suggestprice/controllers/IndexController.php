<?php
class Manojsingh_Suggestprice_IndexController extends Mage_Core_Controller_Front_Action{
    public function suggestAction()
    {
		if($this->getRequest()->getPost())
		{
			try{
			$data = $this->getRequest()->getPost();	
			$adminSubject = Mage::getStoreConfig('suggest_price/suggest/email_subject');
			$adminName = Mage::getStoreConfig('trans_email/ident_general/name'); //sender name
			$adminEmail = Mage::getStoreConfig('trans_email/ident_general/email'); //sender email
			
			$adminreceiveName = Mage::getStoreConfig('suggest_price/suggest/email_name'); //receiver name
			$adminreceiveEmail = Mage::getStoreConfig('suggest_price/suggest/email_address'); //receiver email
			
			//template variables
			$emailTemplateVariables = array();
			$emailTemplateVariables['subject'] = $adminSubject;	
			if(!empty($data['psname'])){
				$emailTemplateVariables['psname'] = $data['psname'];
			}
			if(!empty($data['psemail'])){
				$emailTemplateVariables['psemail'] = $data['psemail'];
			}
			if(!empty($data['psphone'])){
				$emailTemplateVariables['psphone'] = $data['psphone'];
			}
			if(!empty($data['pscomments'])){
				$emailTemplateVariables['pscomments'] = $data['pscomments'];
			}
			if(!empty($data['ps_productname'])){
				$emailTemplateVariables['ps_productname'] = $data['ps_productname'];
			}
			if(!empty($data['ps_productsku'])){
				$emailTemplateVariables['ps_productsku'] = $data['ps_productsku'];
			}
			if(!empty($data['newprice'])){
				$emailTemplateVariables['newprice'] = $data['newprice'];
			}
			if(!empty($data['ps_reduced'])){
				$emailTemplateVariables['ps_reduced'] = $data['ps_reduced'];
			}

			$emailTemplateVariables['admin_name'] = $adminreceiveName;
			$templateId = Mage::getStoreConfig('suggest_price/suggest/email_template');
		
			$sender = Array('name'  => $adminName,
						  'email' => $adminEmail);
			
			$translate  = Mage::getSingleton('core/translate');
			Mage::getModel('core/email_template')
				  ->setTemplateSubject($adminSubject)
				  ->sendTransactional($templateId, $sender, $adminreceiveEmail, $adminreceiveName, $emailTemplateVariables);
			$translate->setTranslateInline(true); 
			
			$success_msg = Mage::getStoreConfig('suggest_price/suggest/success_message');
			Mage::getSingleton('core/session')->addSuccess($success_msg);
			}
			catch (Exception $e) {
				Zend_Debug::dump($e->getMessage());
			}
		}		
		$this->_redirect('/');
    }
}