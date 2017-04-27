<?php

class TM_EasyTabs_Model_Tab extends Mage_Rule_Model_Abstract
{

    const CACHE_TAG = 'easytabs_tab_';
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('easytabs/tab');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Mage_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('rule/action_collection');
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('easytabs/rule_condition_combine');
    }

}
