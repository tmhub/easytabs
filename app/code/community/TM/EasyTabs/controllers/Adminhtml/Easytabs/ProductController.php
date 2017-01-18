<?php

class TM_EasyTabs_Adminhtml_Easytabs_ProductController
    extends TM_EasyTabs_Controller_Adminhtml_Abstract
{

    public function _construct()
    {
        // set flag `product tab`
        $this->productTab = 1;
        parent::_construct();
    }

    public function indexAction()
    {
        $this->_title($this->__('TM'))
            ->_title($this->__('EasyTabs'))
            ->_title($this->__('Product Tabs'));
        parent::indexAction();
    }

}
