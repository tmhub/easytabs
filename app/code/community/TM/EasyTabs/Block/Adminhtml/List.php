<?php

class TM_EasyTabs_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {

        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml_list';

        if (!$this->_isAllowedAction()) {
            $this->_removeButton('add');
        }

        parent::__construct();
    }

    protected function _beforeToHtml()
    {
        $this->_headerText = Mage::helper('easytabs')->__($this->getTitle());
        return parent::_beforeToHtml();
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('templates_master/easytabs');
    }

}
