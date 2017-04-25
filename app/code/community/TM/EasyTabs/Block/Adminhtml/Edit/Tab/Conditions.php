<?php

class TM_EasyTabs_Block_Adminhtml_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Conditions');
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Getter
     *
     * @return TM_SeoTemplates_Model_Template
     */
    public function getEasytabsTab()
    {
        return Mage::registry('easytabs_tab');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
     */
    protected function _prepareForm()
    {
        $easytab = $this->getEasytabsTab();
        $form = new Varien_Data_Form();

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>$this->__('Conditions (leave blank to apply for all)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
        ))->setRule($easytab)
        ->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return TM_SeoTemplates_Block_Adminhtml_List_Edit_Tab_Main
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getEasytabsTab()->getData());
        return parent::_initFormValues();
    }
}
