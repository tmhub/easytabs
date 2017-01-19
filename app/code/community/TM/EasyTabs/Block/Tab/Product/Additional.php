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

    public function getAdditionalData(array $excludeAttr = array())
    {
        $_additional = parent::getAdditionalData($excludeAttr);

        if ($this->getIsGrouped()) {
            $setId = $this->getProduct()->getAttributeSetId(); // Attr Set Id
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            $attributeCodes = array();
            foreach ($groups as $group) {
                $groupName = $group->getAttributeGroupName();
                $groupId = $group->getAttributeGroupId();

                $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                    ->setAttributeGroupFilter($group->getId())
                    ->addVisibleFilter()
                    ->checkConfigurableProducts()
                    ->load();
                if ($attributes->getSize() > 0) {
                    foreach ($attributes->getItems() as $attribute) {
                        /* @var $child Mage_Eav_Model_Entity_Attribute */
                        $attributeCodes[$attribute->getAttributeCode()] = array(
                            'id' => $groupId,
                            'name' => $groupName,
                            'sort_order' => $group->getSortOrder()
                        );
                        // uncomment for debug
                        // if ($attribute->getIsVisibleOnFront() && $attribute->getIsUserDefined()) {
                            // $_additional[$attribute->getAttributeCode()] = array(
                            //     "label" => $attribute->getFrontendLabel(),
                            //     "value" => $attribute->getDefaultValue() . ' xx',
                            //     "code" => $attribute->getAttributeCode()
                            // );
                        // }
                    }
                }
            }

            foreach ($_additional as &$data) {
                $data['group'] = isset($attributeCodes[$data['code']])
                    ? $attributeCodes[$data['code']]
                    : array();
            }
            usort($_additional, function($a, $b) {
                return $a['group']['sort_order'] - $b['group']['sort_order'];
            });
        }

        return $_additional;
    }

}
