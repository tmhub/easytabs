<?php

class TM_EasyTabs_Block_Product extends TM_EasyTabs_Block_Abstract
{

    protected function _getCollection()
    {
        return parent::_getCollection()->addProductTabFilter();
    }

}
