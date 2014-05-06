<?php

class TM_EasyTabs_Block_Tab_Html extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();
        return $processor->filter($this->getContent());
    }
}
