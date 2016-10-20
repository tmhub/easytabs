<?php

class TM_EasyTabs_Model_Tab extends Mage_Core_Model_Abstract
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

}
