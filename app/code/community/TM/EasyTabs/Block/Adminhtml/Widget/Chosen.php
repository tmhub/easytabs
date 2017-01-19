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
        $html = '<script type="text/javascript">'
            . 'new Chosen($("' . $element->getId() . '"), {width: "400px"});'
            . '$("'. $element->getId() .'")'
            . '.up(".entry-edit .fieldset .hor-scroll")'
            . '.setStyle({overflow: "visible"});'
            .'</script>';
        $element->setData('after_element_html', $html);
        return $element;
    }
}
