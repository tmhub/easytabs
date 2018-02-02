<?php

class TM_EasyTabs_Block_Product extends TM_EasyTabs_Block_Abstract
{

    protected function _getCollection()
    {
        return parent::_getCollection()->addProductTabFilter();
    }

    /**
     * Returns show anchor flag
     *
     * @return boolean
     */
    public function getUpdateUrlHash()
    {
        return Mage::getStoreConfigFlag('tm_easytabs/general/update_url_hash');
    }

    public function getHtmlId()
    {
        return 'easytabs-product';
    }

    /**
     * Get product tabs layout name
     *
     * @return string
     */
    public function getTabsLayout()
    {
        return Mage::getStoreConfig('tm_easytabs/general/tabs_layout');
    }

}
