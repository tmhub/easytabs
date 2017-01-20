<?php

class TM_EasyTabs_Block_Adminhtml_Widget_Chosen
    extends Mage_Adminhtml_Block_Widget
{

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html = <<<HTML
<script type="text/javascript">
    new Chosen(
        $("{$element->getId()}"),
        {width: "280px", placeholder_text: "{$this->__('Select Tabs')}"});
    $("{$element->getId()}")
        .up(".entry-edit .fieldset .hor-scroll")
        .setStyle({overflow: "visible"});
    if ($("{$element->getId()}").up("#widget_window_content")) {
        $("{$element->getId()}")
            .up("#widget_window_content")
            .setStyle({overflow: "visible"})
            .up("#widget_window_table_content")
            .setStyle({overflow: "visible"});
    }
</script>
HTML;
        $element->setData('after_element_html', $html);
        return $element;
    }
}
