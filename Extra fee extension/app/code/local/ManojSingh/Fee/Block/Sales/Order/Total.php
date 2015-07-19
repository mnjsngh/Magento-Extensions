<?php
class ManojSingh_Fee_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize Fee total
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getBaseFeeAmount()) {
            $source = $this->getSource();
            $value  = $source->getFeeAmount();
			$total = new Varien_Object(array(
                'code'   => 'fee',
                'strong' => false,
                'label'  => Mage::helper('fee')->getFeeLabel(),
                'value'  => $source instanceof Mage_Sales_Model_Order_Creditmemo ? - $value : $value
            ));
			$after = 'subtotal';
            $this->getParentBlock()->addTotal($total, $after);
        }
        return $this;
    }
}
