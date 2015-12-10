<?php
class Manojsingh_Productquestions_Model_Observer {

    /**
     * @param $observer
     * @return $count, needed for tests
     */
    public function updateProductQuestionsProductsNames($observer) {
        $product = $observer->getEvent()->getProduct();

        if (!$storeId = $product->getStore()->getId())
            $storeId = null;

        $count = 0;
        $count = Mage::getResourceModel('productquestions/productquestions')
                ->setProductTitleById($product->getId(), $product->getName(), $storeId);
        return $count;
    }

    /**
     * @param $observer
     * @return $count, needed for tests
     */
    public function deleteProductQuestionsForProduct($observer) {
        $product = $observer->getEvent()->getProduct();

        if (!$storeId = $product->getStore()->getId())
            $storeId = null;

        $count = 0;
        $count = Mage::getResourceModel('productquestions/productquestions')
                ->deleteByProductId($product->getId(), $storeId);
        return $count;
    }

}
