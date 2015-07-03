<?php

class TM_EasyTabs_Block_Tab_Template extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->createBlock(
            $this->getCustomOption(), $this->getNameInLayout() . '_tab'
        );
        if ($block instanceof Mage_Core_Block_Template) {
            $this->setChild($this->getNameInLayout() . '_tab', $block);
        }
        return parent::_prepareLayout();
    }

    protected function _toHtml()
    {
        $block = $this->getTabBlock();
        if ($block instanceof Mage_Core_Block_Template) {
            return $block->setTemplate($this->getTemplate())
                ->toHtml();
        }
        return '';
    }

    public function getTabBlock()
    {
        return $this->getChild($this->getNameInLayout() . '_tab');
    }

    public function getCount()
    {
        $block = $this->getTabBlock();
        if ($block instanceof Mage_Core_Block_Template) {
            return $block->getCount();
        }
        return;
    }
}