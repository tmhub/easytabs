<?php

class TM_EasyTabs_Block_Tab_Attribute extends Mage_Catalog_Block_Product_View
{
    public function getAttributeCode()
    {
        return $this->getCustomOption();
    }
}