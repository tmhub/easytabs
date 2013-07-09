<?php

class TM_EasyTabs_Block_Tab_Template extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $block = $this->getLayout()
            ->createBlock($this->getCustomOption());

        if ($block instanceof Mage_Core_Block_Template) {
            return $block->setTemplate($this->getTemplate())
                ->toHtml();
        }
        return '';
    }
}