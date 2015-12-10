<?php
class Manojsingh_Productquestions_IndexController extends Mage_Core_Controller_Front_Action {
    /*
     * @var Mage_Catalog_Model_Product current product
     */

    protected $_product = null;

    /*
     * @var Mage_Catalog_Model_Category current category
     */
    protected $_category = null;

    /*
     * Initializes environment
     */

    protected function _initProduct($registerObjects = false) {
        $product = Mage::helper('productquestions')->getCurrentProduct();

        if (!($product instanceof Mage_Catalog_Model_Product)) {
            throw new Exception($this->__('No product selected'));
        }

        $categoryId = (int) $this->getRequest()->getParam('category', false);
        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category
                    && $category instanceof Mage_Catalog_Model_Category
                    && $categoryId == $category->getId()
            ) {
                $product = $product->setCategory($category);
                $this->_category = $category;
                if ($registerObjects)
                    Mage::register('current_category', $category);
            }
        }
        if ($registerObjects) {
            Mage::register('product', $product);
            Mage::register('current_product', $product);
            Mage::register('manojsinghpq_controller', true);
        }
        $this->_product = $product;

        return $this;
    }

    public function indexAction() {
        /*
         * If PQ url rewrite enabled, we disable direct ../index.php/productquestions/.. link
         * If PQ url rewrite disabled, we disable rewrited ../productname-questions.ext link
         */
        $urlRewritesEnabled = Mage::getStoreConfig('productquestions/seo/enable_url_rewrites');
        $pathChanged = ($this->getRequest()->getPathInfo() != $this->getRequest()->getOriginalPathInfo());
        if ($urlRewritesEnabled xor $pathChanged) {
            $this->norouteAction();
            return;
        }


        try {
            $this->_initProduct(true)->loadLayout();
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError($ex->getMessage());
            $this->_redirect('/');
            return;
        }

        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('product', array(
                'label' => $this->_product->getName(),
                'link' => $this->_product->getProductUrl(),
                'readonly' => true,
            ));
            $breadcrumbsBlock->addCrumb('questions', array(
                'label' => $this->__('Product Questions'),
            ));
        }

        $headerTitle = str_replace('%T', $this->_product->getName(), trim(preg_replace("/(\s){2,}/", ' ', Mage::getStoreConfig('productquestions/seo/header_title_pattern'))));
        $metaDescription = str_replace('%D', $this->_product->getMetaDescription(), trim(preg_replace("/(\s){2,}/", ' ', Mage::getStoreConfig('productquestions/seo/header_meta_description_pattern'))));
        $this->getLayout()->getBlock('head')->setTitle($headerTitle);
        $this->getLayout()->getBlock('head')->setDescription($metaDescription);


        $this->getLayout()->getBlock('productquestions')->setQuestionId($this->getRequest()->getParam('qid'));

        $this->renderLayout();
    }

    /*
     * Voting for question helpfullness
     */

    public function voteAction() {
        $session = Mage::getSingleton('core/session');
        $customerSession = Mage::getSingleton('customer/session');

        if (!Mage::getStoreConfig('productquestions/interface/guests_allowed_to_vote')
                && !$customerSession->isLoggedIn()
        ) {
            $session->addNotice($this->__('Guests are not allowed to vote!'));
            $this->_redirectReferer();
            return;
        }

        try {
            $id = $this->getRequest()->getParam('id');
            $value = $this->getRequest()->getParam('value');

            $customerId = Mage::helper('productquestions')->getCustomerId();

            $votedQuestions = $customerSession->getVotedQuestions();

            if (!$votedQuestions) {
                $votedQuestions = Mage::getModel('core/cookie')->get('manojsinghpq_votes_' . $customerId);
            }

            if ($votedQuestions
                    && in_array($id, explode(',', $votedQuestions))
            ) {
                $session->addNotice($this->__('You have already voted on this question!'));
                $this->_redirectReferer();
                return;
            } else {
                Mage::getModel('productquestions/productquestions')->setId($id)->vote($value);
                $votedQuestions = $votedQuestions . ($votedQuestions ? ',' : '') . $id;
                $customerSession->setVotedQuestions($votedQuestions);
                Mage::getModel('core/cookie')->set('manojsinghpq_votes_' . $customerId, $votedQuestions, true);
            }
            $session->addSuccess($this->__('Your voice has been accepted. Thank you!'));
        } catch (Exception $e) {
            Mage::logException($e);
            $session->addError($this->__('Unable to vote. Please, try again later.'));
        }
        $this->_redirectReferer();
    }
	
	public function reportAction() {
        $session = Mage::getSingleton('core/session');
        $customerSession = Mage::getSingleton('customer/session');

        if (!Mage::getStoreConfig('productquestions/interface/guests_allowed_to_vote')
                && !$customerSession->isLoggedIn()
        ) {
            $session->addNotice($this->__('Guests are not allowed to report!'));
            $this->_redirectReferer();
            return;
        }

        try {
            $id = $this->getRequest()->getParam('id');
            $value = $this->getRequest()->getParam('value');

            $customerId = Mage::helper('productquestions')->getCustomerId();

            $reportedQuestions = $customerSession->getReportedQuestions();

            if (!$reportedQuestions) {
                $reportedQuestions = Mage::getModel('core/cookie')->get('rpq_votes_' . $customerId);
            }

            if ($reportedQuestions
                    && in_array($id, explode(',', $reportedQuestions))
            ) {
                $session->addNotice($this->__('You have already reported on this question!'));
                $this->_redirectReferer();
                return;
            } else {
                Mage::getModel('productquestions/productquestions')->setId($id)->report($value);
                $reportedQuestions = $reportedQuestions . ($reportedQuestions ? ',' : '') . $id;
                $customerSession->setReportedQuestions($reportedQuestions);
                Mage::getModel('core/cookie')->set('rpq_votes_' . $customerId, $reportedQuestions, true);
            }
            $session->addSuccess($this->__('Your report has been accepted. Thank you!'));
        } catch (Exception $e) {
            Mage::logException($e);
            $session->addError($this->__('Unable to report. Please, try again later.'));
        }
        $this->_redirectReferer();
    }

    /*
     * Posting a question
     */

    public function postAction() {
        $productId = $this->getRequest()->getParam('id');
        $url = Mage::getUrl('productquestions/index', array('id' => $productId));

        if (!Manojsingh_Productquestions_Helper_Data::checkIfGuestsAllowed())
            return $this->_redirectReferer();
        try {
            $this->_initProduct();
        } catch (Exception $ex) {
            Mage::getSingleton('core/session')->addError($ex->getMessage());
        }
        $data = $this->getRequest()->getPost();
        if ($this->_product && !empty($data)) {
            $session = Mage::getSingleton('core/session');

            $question = Mage::getModel('productquestions/productquestions')->setData($data);

            $validate = $question->validate();
            if ($validate === true) {
                $store = Mage::app()->getStore();
                $storeId = $store->getId();

                try {
                    if (Mage::getStoreConfig('productquestions/interface/customer_status', $storeId)
                            && isset($data['question_status'])
                    )
                        $question->setQuestionStatus(intval(@$data['question_status']));
                    elseif (Mage::getStoreConfig('productquestions/interface/customer_status'))
                        $question->setQuestionStatus(Manojsingh_Productquestions_Model_Status::STATUS_PRIVATE);

                    $question
                            ->setQuestionProductId($this->_product->getId())
                            ->setQuestionAuthorName($data['question_author_name'])
                            ->setQuestionAuthorEmail($data['question_author_email'])
                            ->setQuestionProductName($this->_product->getName())
                            ->setQuestionText($data['question_text'])
                            ->setQuestionDate(now())
                            ->setQuestionStoreId($storeId)
                            ->setQuestionStoreIds($storeId)
                            ->save();

                    $session->addSuccess($this->__('Your question has been accepted for moderation'));
                    $session->setProductquestionsData(false);

                    if (Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_RECIPIENT)) {
                        /* Now send email to admin about new question */
                        $mailTemplate = Mage::getModel('core/email_template');
                        try {
                            $sender = Mage::helper('productquestions')->getSender();
                            if (method_exists($mailTemplate->getMail(), 'setReplyTo')) {
                                $mailTemplate->getMail()->setReplyTo($sender['mail'], $sender['name']);
                            } else {
                                $mailTemplate->getMail()->addHeader('Reply-To', $sender['mail']);
                            }
                        } catch (Exception $ex) {
                            
                        }

                        $mailTemplate->setDesignConfig(array(
                                    'area' => 'adminhtml',
                                    'store' => Mage_Core_Model_App::ADMIN_STORE_ID,
                                ))
                                ->sendTransactional(
                                        Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_ADMIN_TEMPLATE), Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_SENDER), Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_RECIPIENT), null, array('data' => $question), $storeId
                        );

                        if (!$mailTemplate->getSentSuccess()) { //throw new Exception();
                            Mage::log($this->__('An error occured while sending Product Questions email from \'%s\' to admin \'%s\' using template \'%s\', asked by \'%s\', the question is \'%s\'', Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_SENDER), Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_RECIPIENT), Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_ADMIN_TEMPLATE), $data['question_author_email'], $question->getQuestionText()));
                        }
                    }

                    // processing standard Newsletter subscription
                    if (isset($data['subscribe_newsletter'])
                            && $data['subscribe_newsletter']
                    )
                        Mage::helper('productquestions')->subscribeCustomer($data['question_author_email']);

                    // processing Advanced Newsletter segment subscription
                    if (isset($data['anl_segments']))
                        Mage::helper('productquestions')->subscribeAdvancedNewsletterSegment(
                                $data['question_author_email'], $data['question_author_name'], $data['anl_segments']
                        );

                    // sending a reply to customer
                    if (Mage::getStoreConfig('productquestions/autorespond/status')) {
                        $mailTemplate = Mage::getModel('core/email_template');
                        try {
                            $sender = Mage::helper('productquestions')->getSender();
                            if (method_exists($mailTemplate->getMail(), 'setReplyTo')) {
                                $mailTemplate->getMail()->setReplyTo($sender['mail'], $sender['name']);
                            } else {
                                $mailTemplate->getMail()->addHeader('Reply-To', $sender['mail']);
                            }
                            $mailTemplate->setFromEmail($sender['mail']);
                        } catch (Exception $ex) {
                            
                        }

                        $categoryId = $this->getRequest()->getParam('category_id');
                        $mailVariables = array(
                            'data' => $question,
                            'question_text' => $data['question_text'],
                            'customer_name' => $data['question_author_name'],
                            'customer_email' => $data['question_author_email'],
                            'date_asked' => Mage::app()->getLocale()->date($question->getQuestionDate(), Varien_Date::DATETIME_INTERNAL_FORMAT)->toString(
                                    Mage::app()->getLocale()->getDateTimeFormat(
                                            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM)),
                            'store' => $store,
                            'product' => $this->_product,
                            'product_name' => $this->_product->getName(),
                            'category' => Mage::getModel('catalog/category')->load($categoryId),
                            'customer' => Mage::getModel('customer/customer')
                                    ->setWebsiteId(Mage::app()->getWebsite()->getId())
                                    ->loadByEmail($data['question_author_email']),
                            'customer_subscribed' => (isset($data['subscribe_newsletter']) && $data['subscribe_newsletter']),
                            'product_url' => $this->_product->getProductUrl(),
                        );

                        $mailTemplate
                                ->setDesignConfig(
                                        array('area' => 'frontend', 'store' => $store)
                                )
                                ->sendTransactional(
                                        Mage::getStoreConfig('productquestions/autorespond/email_template'), Mage::getStoreConfig(Manojsingh_Productquestions_Model_Source_Config_Path::EMAIL_SENDER), $data['question_author_email'], $data['question_author_name'], $mailVariables, $storeId
                        );

                        if (!$mailTemplate->getSentSuccess()) { //throw new Exception(); 
                            $session->addError($this->__('An error occured, while sending a reply message to you.'));
                        }
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::getSingleton('core/session')->setProductquestionsData($data);
                    Mage::log($e);
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
                }
            } else {
                Mage::getSingleton('core/session')->setProductquestionsData($data);

                if (is_array($validate))
                    foreach ($validate as $errorMessage)
                        $session->addError($errorMessage);
                else
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
            }
        }
        if (Mage::getVersion() == '1.4.2.0')
            $this->_redirectUrl(Mage::helper('productquestions')->getQuestionsLink($productId, $url));
        else
            $this->_redirectReferer();
    }

}