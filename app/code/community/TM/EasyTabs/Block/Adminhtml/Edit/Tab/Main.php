<?php

class TM_EasyTabs_Block_Adminhtml_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _getBlockTypes()
    {
        return array(
            ''                                 => 'Anything',
            'catalog/product_view_description' => 'Product Description',
            'catalog/product_view_attributes'  => 'Additional Information',
            'catalog/product_list_upsell'      => 'We Also Recommend',
            'catalog/product_list_related'     => 'Related Products',
            'tag/product_list'                 => 'Product Tags',
            'review/product_view_list'         => 'Product\'s Review',

            'easytabs/tab_attribute'           => 'EasyTabs Product\'s Attribute',
            'easytabs/tab_cms'                 => 'EasyTabs Static Block',

        );
    }


    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        if (Mage::registry('easytabs_tab_data') ) {
            $model = Mage::registry('easytabs_tab_data');
        }

        $fieldset = $form->addFieldset(
            'trap_general_form',
            array('legend' => Mage::helper('easytabs')->__('General Details'))
        );

        $fieldset->addField('id', 'hidden', array(
            'name'     => 'id',
        ));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));

//        $fieldset->addField('alias', 'text', array(
//            'label'     => Mage::helper('easytabs')->__('Alias'),
//            'class'     => 'required-entry',
//            'required'  => true,
//            'name'      => 'alias',
//        ));

        $fieldset->addField('alias', 'hidden', array(
            'name'     => 'alias',
        ));

        $block = $model->getBlock();
        $blockTypes = $this->_getBlockTypes();
        $blockType  = isset($blockTypes[$block]) ? $block : '';

        $model->setBlockType($blockType);

        $fieldset->addField('block_type', 'select', array(
            'label'     => Mage::helper('easytabs')->__('Block Type'),
            'title'     => Mage::helper('easytabs')->__('Block Type'),
            'name'      => 'block_type',
//            'required'  => true,
            'options'   => $this->_getBlockTypes(),
        ));

        $fieldset->addField('block', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Block'),
            'class'     => 'required-entry',
            'required'  => true,
            'disabled'  => true,
            'name'      => 'block',
        ));

        $el = $fieldset->addField('custom_option', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Custom Option'),
//            'class'     => 'required-entry',
//            'required'  => true,
            'disabled'  => true,
            'name'      => 'custom_option',
        ));

        $el->setAfterElementHtml(
        "<script type=\"text/javascript\">
            Event.observe(window, 'load', function(){
                function hasCustomOption()
                {
                    var element = $('block_type');
                    if (!element) {
                        return false;
                    }
                    if ('easytabs/tab_attribute' == element.value
                        || 'easytabs/tab_cms' == element.value) {

                        return true;
                    }
                    return false;
                }
                if (!hasCustomOption()) {
                    $('custom_option').up('tr').hide();
                } else {
                    $('custom_option').enable();
                }
                $('block_type').observe('change', function(event){
                    var element = $(Event.element(event)), target = $('block');
                    if ('' == element.value) {
                        target.enable();
                        target.setValue('');
                    } else {
                        target.disable();
                        target.setValue(element.value);
                    }

                    if (hasCustomOption()) {
                        $('custom_option').up('tr').show();
                        $('custom_option').enable();
                    } else {
                        $('custom_option').up('tr').hide();
                        $('custom_option').disable();
                    }
                });
            });
        </script>");

        $fieldset->addField('template', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Template'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'template',
        ));

        $fieldset->addField('unset', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Unset'),
//            'class'     => 'required-entry',
            'name'      => 'unset',
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Sort Order'),
//            'class'     => 'required-entry',
            'name'      => 'sort_order',
            'class' => 'validate-digits',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('easytabs')->__('Status'),
            'title'     => Mage::helper('easytabs')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => Mage::getSingleton('easytabs/config_status')->getOptionHash(),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_id', 'hidden', array(
                'name'  => 'website_id',
                'value' => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'  => 'website_id',
                'label' => Mage::helper('easytabs')->__('Websites'),
                'title' => Mage::helper('easytabs')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('checkout')->__('Store View'),
                'title'     => Mage::helper('checkout')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

//        Zend_Debug::dump($model->getData());
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