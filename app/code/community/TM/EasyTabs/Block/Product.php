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

}
