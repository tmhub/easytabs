<?php

class TM_EasyTabs_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {

        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml_list';

        parent::__construct();
    }

    protected function _beforeToHtml()
    {
        $this->_headerText = Mage::helper('easytabs')->__($this->getTitle());
        return parent::_beforeToHtml();
    }

}
