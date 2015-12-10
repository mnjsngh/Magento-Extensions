<?php
class Manojsingh_Productquestions_Model_Urlrewrite extends Mage_Core_Model_Url_Rewrite {
    /*
     * Suffix of the PQ question page URLs
     */
    const SEO_SUFFIX = '-questions';

    public function rewrite(Zend_Controller_Request_Http $request=null, Zend_Controller_Response_Http $response=null) {
        $result = parent::rewrite($request, $response);
        if (false !== $result)
            return $result;

        if (!Mage::isInstalled())
            return false;

        if (is_null($request))
            $request = Mage::app()->getFrontController()->getRequest();

        $initialRequestPath = trim($request->getPathInfo(), '/');
        $suffix = Mage::getStoreConfig('catalog/seo/product_url_suffix');
        $pqSuffix = self::SEO_SUFFIX . $suffix;
        if ($pqSuffix == substr($initialRequestPath, strlen($initialRequestPath) - strlen($pqSuffix))) {
            $requestPath = substr($initialRequestPath, 0, strlen($initialRequestPath) - strlen($pqSuffix));

            if (is_null($this->getStoreId()) || false === $this->getStoreId())
                $this->setStoreId(Mage::app()->getStore()->getId());

            $this->loadByRequestPath($requestPath . $suffix);

            if ($this->getId()) {
                $request->setPathInfo('productquestions/index/index/');
                $request->setParam('id', $this->getProductId());
                if ($this->getCategoryId())
                    $request->setParam('category', $this->getCategoryId());

                if (Mage::getConfig('productquestions/seo/cache_requests')) {
                    try {
                        $this
                                ->setUrlRewriteId(null)
                                ->setRequestPath($initialRequestPath)
                                ->setTargetPath('productquestions/index/index/id/' . $this->getProductId() . '/category/' . $this->getCategoryId())
                                ->setIdPath($this->getIdPath() . '/questions')
                                ->setIsSystem(0)
                                ->save();
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }

                return parent::rewrite($request, $response);
            }
        }
        return false;
    }

}