<?php

class TM_EasyTabs_Model_Resource_Tab
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('easytabs/tab', 'id');
    }

}
