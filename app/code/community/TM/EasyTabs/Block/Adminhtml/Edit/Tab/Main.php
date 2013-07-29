<?php

class TM_EasyTabs_Block_Adminhtml_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _getBlockTypes()
    {
        $widgets = Mage::getModel('widget/widget')->getWidgetsArray();
        $types = array();
        foreach ($widgets as $widget) {
            if (0 === strpos($widget['code'], 'easytabs_')) {
                $types[$widget['type']] = $widget['name'];
            }
        }
        return $types;
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
        if (!isset($blockTypes[$block])) {
            $model->setBlock('easytabs/tab_template');
        }
        $model->setBlockType($model->getBlock());

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

        $sortOrder = $model->getSortOrder();
        if (empty($sortOrder)) {
            $model->setSortOrder(0);
        }
        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('easytabs')->__('Sort Order'),
//            'class'     => 'required-entry',
            'name'      => 'sort_order',
            'class'     => 'validate-digits',
        ));

        $el = $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('easytabs')->__('Status'),
            'title'     => Mage::helper('easytabs')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => Mage::getSingleton('easytabs/config_status')->getOptionHash(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('checkout')->__('Store View'),
                'title'     => Mage::helper('checkout')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            if ($renderer) {
                $field->setRenderer($renderer);
            }
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $values = $model->getData();
        $values = isset($values['id']) ?
            Mage::helper('core')->jsonEncode($values) : 'false';

        $el->setAfterElementHtml(
        "<script type=\"text/javascript\">
            Event.observe(window, 'load', function(){

                WidgetOptions = function(){
                    var _values = {$values},
                    _url = '{$this->getUrl('*/widget/loadOptions')}';

                    function _insertHtml(html){
                        var container = $('widget_options');
                        if (!container) {
                            $('easytabs_tabs_main_section_content').insert({
                                bottom: '<div id=\"widget_options\"></div>'
                            });
                        }
                        $('widget_options').innerHTML = html;
                    }
                    return {
                        load: function(type){
                            var params = {widget_type: type};
                            if (_values) {
                                params['values'] = _values;
                            }
                            new Ajax.Request(_url, {
                                parameters: {widget: Object.toJSON(params)},
                                onSuccess: function(transport) {
                                    try {
                                        _insertHtml(transport.responseText);
                                    } catch(e) {
                                        alert(e.message);
                                    }
                                }.bind(this)
                            });
                        }
                    }
                }();

                $('block_type').observe('change', function(event){
                    var value = $(this).getValue();
                    WidgetOptions.load(value);
                    $('block').setValue(value);
                });
                WidgetOptions.load('{$model->getData('block')}');
            });
        </script>");

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