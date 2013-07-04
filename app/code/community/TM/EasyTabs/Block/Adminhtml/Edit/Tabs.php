<?php

class TM_EasyTabs_Block_Adminhtml_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('easytabs_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('easytabs')->__('Tab Information'));
    }
}