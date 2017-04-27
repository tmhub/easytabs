<?php

class TM_EasyTabs_Model_Rule_Condition_Customer
    extends Mage_Rule_Model_Condition_Abstract

{

    public function loadAttributeOptions()
    {
        $options = array(
            'customer_group' => Mage::helper('customer')->__('Customer Groups')
        );
        $this->setAttributeOption($options);
        return $this;
    }

    /**
     * Retrieve value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        if ($this->getAttribute() == 'customer_group') {
            return 'select';
        }
        return parent::getValueElementType();
    }

    /**
     * Retrieve select option values
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $options = array();
        $collection = Mage::getModel('customer/group')->getCollection();
        foreach ($collection as $group) {
            $options[] = array(
                'label' => $group->getCustomerGroupCode(),
                'value' => $group->getId(),
            );
        }
        return $options;
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getInputType()
    {
        if ($this->getAttribute() == 'customer_group') {
            return 'select';
        }
        return parent::getInputType();
    }

}
