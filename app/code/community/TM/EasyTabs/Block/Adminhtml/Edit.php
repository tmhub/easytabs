<?php

class TM_EasyTabs_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'easytabs';
        $this->_controller = 'adminhtml';

        $model  = Mage::registry('easytabs_tab_data');
        $values = $model->getData();
        $values = isset($values['id']) ? Mage::helper('core')->jsonEncode($values) : 'false';
        $this->_formScripts[] = "
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
        ";
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
