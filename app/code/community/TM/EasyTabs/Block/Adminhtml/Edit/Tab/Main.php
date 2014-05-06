<?php

class TM_EasyTabs_Block_Adminhtml_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _getBlockTypes()
    {
        $tabs = Mage::getSingleton('easytabs/tabs')->getTabsArray();
        $types = array();
        foreach ($tabs as $tab) {
            $types[$tab['type']] = $tab['name'];
        }
        return $types;
    }


    protected function _prepareForm()
    {
        $model = Mage::registry('easytabs_tab_data');

        $form  = new Varien_Data_Form();

        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'trap_general_form',
            array('legend' => Mage::helper('easytabs')->__('General Details'))
        );

        $fieldset->addField('id', 'hidden', array(
            'name' => 'id',
        ));

        $fieldset->addField('title', 'text', array(
            'label'    => Mage::helper('easytabs')->__('Title'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'title',
        ));

        $fieldset->addField('alias', 'text', array(
            'label'    => Mage::helper('easytabs')->__('Alias'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'alias',
        ));

        $block = $model->getBlock();
        $blockTypes = $this->_getBlockTypes();
        if (!isset($blockTypes[$block])) {
            $model->setBlock('easytabs/tab_html');
        }
        $model->setBlockType($model->getBlock());

        $fieldset->addField('block_type', 'select', array(
            'label'   => Mage::helper('easytabs')->__('Block Type'),
            'title'   => Mage::helper('easytabs')->__('Block Type'),
            'name'    => 'block_type',
            'options' => $this->_getBlockTypes(),
        ));

        $fieldset->addField('block', 'text', array(
            'label'    => Mage::helper('easytabs')->__('Block'),
            'class'    => 'required-entry',
            'required' => true,
            'disabled' => true,
            'name'     => 'block',
        ));

        $sortOrder = $model->getSortOrder();
        if (empty($sortOrder)) {
            $model->setSortOrder(0);
        }
        $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('easytabs')->__('Sort Order'),
            'name'  => 'sort_order',
            'class' => 'validate-digits',
        ));

        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('easytabs')->__('Status'),
            'title'    => Mage::helper('easytabs')->__('Status'),
            'name'     => 'status',
            'required' => true,
            'options'  => Mage::getSingleton('easytabs/config_status')->getOptionHash(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'     => 'store_id',
                'label'    => Mage::helper('checkout')->__('Store View'),
                'title'    => Mage::helper('checkout')->__('Store View'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            if ($renderer) {
                $field->setRenderer($renderer);
            }
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'  => 'store_id',
                'value' => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $form->setValues($model->getData());

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('easytabs')->__('Main');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('easytabs')->__('Main');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
