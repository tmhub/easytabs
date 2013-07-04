<?php

class TM_EasyTabs_Block_Tabs extends Mage_Catalog_Block_Product_View_Tabs
{

    protected function _getCollection()
    {
        $collection = new TM_EasyTabs_Model_Config_Collection();
        $storeId = Mage::app()->getStore()->getStoreId();
        $websiteId = Mage::app()->getWebsite()->getId();
        return $collection
            ->setOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFieldToFilter('status', array('eq' => 1))
            ->addFieldToFilter('website_id', array('in' => array($websiteId, 0)))
            ->addFieldToFilter('store_id', array('in' => array($storeId, 0)))
            ;
    }

    protected function _prepareLayout()
    {
        foreach ($this->_getCollection() as $tab) {

            $this->addTab(
                $tab->getAlias(),
                $tab->getTitle(),
                $tab->getBlock(),
                $tab->getTemplate()
            );
            //set custom option
            $this->getChild($tab->getAlias())
                ->setCustomOption(
                    $tab->getCustomOption()
                );

            //remove
            list($blockName, $alias) = explode('::', $tab->getUnset());
            $block =  $this->getLayout()->getBlock($blockName);
            if ($block) {
                $block->unsetChild($alias);
            }
        }


        return parent::_prepareLayout();
    }
}