<?php

class TM_EasyTabs_Model_Config_Status extends Varien_Object
{
    const STATUS_DISABLE = 0;
    const STATUS_ENABLE	 = 1;


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
            self::STATUS_DISABLE => Mage::helper('easytabs')->__('Disable'),
            self::STATUS_ENABLE  => Mage::helper('easytabs')->__('Enable'),
        );
    }
}