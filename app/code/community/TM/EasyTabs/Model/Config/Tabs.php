<?php

class TM_EasyTabs_Model_Config_Tabs //extends Varien_Object
{
    const STATUS_DISABLE = 0;
    const STATUS_ENABLE  = 1;

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
        $collection = Mage::getModel('easytabs/tab')->getCollection()
            ->addWidgetTabFilter();

        // \Zend_Debug::dump($collection->getFirstItem()->getData());
        $res = array();
        foreach ($collection as $tab) {
            $res[$tab['alias']] = $tab['title'];
        }
        return $res;
    }
}
