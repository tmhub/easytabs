<?php

class TM_EasyTabs_Block_Tab_Product_Tags extends Mage_Tag_Block_Product_List
{
    protected function _prepareLayout()
    {
        $wrapper = $this->getLayout()->getBlock('product.tag.list.list.before');
        if ($wrapper) {
            $this->setChild('list_before', $wrapper);
        }
        return parent::_prepareLayout();
    }
}
