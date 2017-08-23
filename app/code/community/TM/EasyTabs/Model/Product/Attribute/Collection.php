<?php

class TM_EasyTabs_Model_Product_Attribute_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Attribute_Collection
{
    protected function _toOptionArray($valueField = 'attribute_code', $labelField = 'frontend_label', $additional = array())
    {
        $this->addVisibleFilter();
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
