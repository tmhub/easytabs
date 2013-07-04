<?php

class TM_EasyTabs_Block_Tab_Cms extends Mage_Catalog_Block_Product_View
{
    public function getCmsBlockId()
    {
        return $this->getCustomOption();
    }
}