<?php

class TM_EasyTabs_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml';

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save'
        ), -100);
    }

    public function getHeaderText()
    {
        $data = Mage::registry('easytabs_tab');
        if ($data && $data->getId()) {
            return Mage::helper('easytabs')->__(
                "Edit Tab # %s", $data->getTitle()
            );
        }
        return Mage::helper('easytabs')->__('Add New Tab');
    }

    /**
     * Prepare Layout Content
     *
     * @return TM_EasyTabs_Block_Adminhtml_Edit
     */
    protected function _prepareLayout()
    {
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $model  = Mage::registry('easytabs_tab');
        $values = $model->getData();
        $values = isset($values['id']) ? Mage::helper('core')->jsonEncode($values) : 'false';
        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action + 'back/1');
            }

            EasytabsTabOptions = function() {
                var _values = {$values},
                _url = '{$this->getUrl('*/*/loadTabOptions')}';

                function _insertHtml(html) {
                    var container = $('widget_options');
                    if (!container) {
                        $('easytabs_tabs_main_section_content').insert({
                            bottom: '<div id=\"widget_options\"></div>'
                        });
                    }
                    $('widget_options').innerHTML = html;
                }
                return {
                    load: function(type) {
                        var params = {widget_type: type};
                        if (_values && _values['block'] == type) {
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

            $('block_type').observe('change', function(event) {
                var value = $(this).getValue();
                EasytabsTabOptions.load(value);
                $('block').setValue(value);
            });
            EasytabsTabOptions.load($('block_type').getValue());

            Validation.add('validate-data', 'Please use only letters (a-z or A-Z), numbers (0-9) or underscore(_) in this field, first character should be a letter.', function (v) {
                if(v != '' && v) {
                    return /^[A-Za-z]+[A-Za-z0-9_-]+$/.test(v);
                }
                return true;
            });
        ";

        return parent::_prepareLayout();
    }
}
