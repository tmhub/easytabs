<?php

class TM_EasyTabs_Block_Tab_Cms extends Mage_Core_Block_Template
{
    public function getCmsBlockId()
    {
        return $this->getCustomOption();
    }
}