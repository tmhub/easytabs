<?php

class TM_EasyTabs_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml';

        parent::__construct();

        // $this->_updateButton('save', 'label', Mage::helper('easytabs')->__('Save'));
        // $this->_updateButton('delete', 'label', Mage::helper('easytabs')->__('Delete'));

    }

    public function getHeaderText()
    {
        $data = Mage::registry('easytabs_tab_data');
        if ($data && $data->getId()) {

            return Mage::helper('easytabs')->__(
                "Edit Tab # %s", $data->getTitle()
            );
        }
        return Mage::helper('easytabs')->__('Add New Tab');
    }
}