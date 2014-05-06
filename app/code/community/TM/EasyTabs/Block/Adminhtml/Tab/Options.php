<?php

class TM_EasyTabs_Block_Adminhtml_Tab_Options extends Mage_Widget_Block_Adminhtml_Widget_Options
{
    /**
     * Add fields to main fieldset based on specified tab type
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function addFields()
    {
        // get configuration node and translation helper
        if (!$this->getWidgetType()) {
            Mage::throwException($this->__('Widget Type is not specified'));
        }
        $config = Mage::getSingleton('easytabs/tabs')->getConfigAsObject($this->getWidgetType());
        if (!$config->getParameters()) {
            return $this;
        }
        $module = $config->getModule();
        $this->_translationHelper = Mage::helper($module ? $module : 'widget');
        foreach ($config->getParameters() as $parameter) {
            $this->_addField($parameter);
        }

        return $this;
    }
}
