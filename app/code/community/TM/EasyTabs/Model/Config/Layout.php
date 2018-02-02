<?php

class TM_EasyTabs_Model_Config_Layout extends Varien_Object
{
    const LAYOUT_COLLAPSED = 'collapsed';
    const LAYOUT_EXPANDED = 'expanded';


    public function toOptionArray()
    {
        $res = array();
        foreach ($this->toOptionHash() as $value => $label) {
            $res[] = array('value' => $value, 'label' => $label);
        }
        return $res;
    }

    public function toOptionHash()
    {
        return array(
            self::LAYOUT_COLLAPSED => Mage::helper('easytabs')->__('Collapsed tabs (traditional layout)'),
            self::LAYOUT_EXPANDED => Mage::helper('easytabs')->__('Expanded tabs'),
        );
    }
}