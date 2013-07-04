<?php

class TM_EasyTabs_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
//        Zend_Debug::dump(__LINE__);
//        die;
        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml_list';

        $this->_headerText = Mage::helper('easytabs')->__('Tabs');
//        $this->_addButtonLabel = Mage::helper('easytabs')->__('set a trap');
        parent::__construct();
    }
}