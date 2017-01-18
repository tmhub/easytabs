<?php

class TM_EasyTabs_Adminhtml_Easytabs_WidgetController
    extends TM_EasyTabs_Controller_Adminhtml_Abstract
{

    public function indexAction()
    {
        $this->_title($this->__('TM'))
            ->_title($this->__('EasyTabs'))
            ->_title($this->__('Widget Tabs'));
        parent::indexAction();
    }

}
