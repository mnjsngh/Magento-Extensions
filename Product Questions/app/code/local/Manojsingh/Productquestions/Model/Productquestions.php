<?php
class Manojsingh_Productquestions_Model_Productquestions extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('productquestions/productquestions');
        $this->setIdFieldName('question_id');
    }

    /*
     * Returns the ID of the product asked about
     * @return int
     */

    public function getProductId() {
        return $this->getData('question_product_id');
    }

    /*
     * Returns question stripped from line breaks
     * @result string Question text
     */

    public function getQuestionText() {
        return preg_replace('/<br[^>]*>/i', '', $this->_data['question_text']);
    }

    /*
     * Returns reply text stripped from line breaks
     * @result string Reply text
     */

    public function getQuestionReplyText() {
        return preg_replace('/<br[^>]*>/i', '', $this->_data['question_reply_text']);
    }

    /*
     * Validates question post data
     * @return bool|array TRUE if everything is OK, or array containing error messages
     */

    public function validate() {
        $errors = array();
        $helper = Mage::helper('productquestions');

        if (preg_match('/^1.3/', Mage::getVersion())) {
            if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/', $this->getQuestionAuthorEmail()))
                $errors[] = $helper->__('Please specify valid email address');
        }
        else {
            if (!Zend_Validate::is($this->getQuestionAuthorEmail(), 'EmailAddress'))
                $errors[] = $helper->__('Please specify valid email address');
        }

        if (!Zend_Validate::is($this->getQuestionAuthorName(), 'NotEmpty'))
            $errors[] = $helper->__('Nickname can\'t be empty');

        if (!Zend_Validate::is($this->getQuestionText(), 'NotEmpty'))
            $errors[] = $helper->__('Question text can\'t be empty');

        if (Mage::getSingleton('core/session')->getManojsinghProductQuestionsAntiSpamCode()
                !== $this->getQuestionAntispamCode()
        )
            $errors[] = $helper->__('Antispam code is invalid. Please, check if JavaScript is enabled in your browser settings.');

        if (empty($errors))
            return true;
        else
            return $errors;
    }

    /*
     * Calls resource method to update the question rating
     * @param int Voting value
     */

    public function vote($value) {
        $this->getResource()->vote($this->getId(), $value);
        return $this;
    }
	
	public function report($value) {
        $this->getResource()->report($this->getId(), $value);
        return $this;
    }

    /*
     * Returns link to question reply page
     * @return string URL
     */

    public function getAdminUrl() {
        return Mage::getSingleton('adminhtml/url')->getUrl(
                        'productquestions_admin/adminhtml_index/reply', array('id' => $this->getQuestionId()));
    }

    /*
     * Returns presence of answer ID in the list of tagged replies
     * @return bool TRUE if visitor voted to this answer, and FALSE if not
     */

    public function IsVoted() {

        $votedQuestions = Mage::getSingleton('customer/session')->getVotedQuestions();
        if (in_array($this->getId(), (array) explode(',', $votedQuestions))) {
            return true;
        }

        $customerId = Mage::helper('productquestions')->getCustomerId();
        $votedQuestions = Mage::getModel('core/cookie')->get('manojsinghpq_votes_' . $customerId);

        if ($votedQuestions) {
            $votedQuestions = (array) explode(',', $votedQuestions);
            if (in_array($this->getId(), $votedQuestions)) {
                return true;
            }
        }
        return false;
    }
	
	public function IsReported() {

        $reportedQuestions = Mage::getSingleton('customer/session')->getReportedQuestions();
        if (in_array($this->getId(), (array) explode(',', $reportedQuestions))) {
            return true;
        }

        $customerId = Mage::helper('productquestions')->getCustomerId();
        $reportedQuestions = Mage::getModel('core/cookie')->get('rpq_votes_' . $customerId);

        if ($reportedQuestions) {
            $reportedQuestions = (array) explode(',', $reportedQuestions);
            if (in_array($this->getId(), $reportedQuestions)) {
                return true;
            }
        }
        return false;
    }

}
