<?php

class TM_EasyTabs_Block_Tab_Product_Additional
    extends Mage_Catalog_Block_Product_View_Attributes
{

    protected function _construct()
    {
        parent::_construct();
        $this->setData(
            'is_grouped',
            Mage::getStoreConfig('tm_easytabs/general/group_attributes')
        );
    }

}
