<?php

class TM_EasyTabs_Block_Adminhtml_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('status' => '1'));
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/easytabs_widget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $block = Mage::getModel('cms/block')->load($element->getValue());
            if ($block->getId()) {
                $chooser->setLabel($block->getTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var templateChild = var Template(\'<li class="search-choice">#{inner}</li>\');
                var trElement = Event.findElement(event, "tr");
                var tabTitle = trElement.down("td").next().innerHTML;
                var ulChosen = $("modal_dialog_message").down(".chosen-choices");
                ulChosen.insert(templateChild.evaluate({"inner": tabTitle}));
                console.log(event);return;
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML;
                '.$chooserJsObject.'.setElementValue(blockId);
                '.$chooserJsObject.'.setElementLabel(blockTitle);
                '.$chooserJsObject.'.close();
            }
        ';

        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var tabId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var field = $("modal_dialog_message").down(".chosen-choices");
                var alreadyExist = false;
                field.select(".search-choice .tab-id").each(function (el){
                    if (el.innerHTML == tabId) {
                        alreadyExist = true;
                    }
                });
                if (alreadyExist) {
                    return;
                }
                var tabTitle = trElement.down("td").next().innerHTML.trim();
                var templateChild = new Template(
                    \'<li class="search-choice">\'
                    + \'<span class="tab-id" style="display: none">#{id}</span>\'
                    + \'<span class="tab-title">#{title}</span>\'
                    + \'<a class="search-choice-close" title="Remove tab"></a>\'
                    +\'</li>\'
                    );
                field.insert(templateChild.evaluate({
                    "id": tabId,
                    "title": tabTitle
                }));
            }
        ';
        return $js;
    }

    /**
     * Prepare Easytabs Widget Tabs collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('easytabs/tab')
            ->getCollection()
            ->addWidgetTabFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for Tabs grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('easytabs')->__('ID'),
            'align'     => 'right',
            'index'     => 'id',
            'width'     => 50
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('easytabs')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        // $this->addColumn('chooser_identifier', array(
        //     'header'    => Mage::helper('cms')->__('Identifier'),
        //     'align'     => 'left',
        //     'index'     => 'identifier'
        // ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('easytabs')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('cms')->__('Disabled'),
                1 => Mage::helper('cms')->__('Enabled')
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl(
                '*/easytabs_widget/chooser',
                array('_current' => true)
            );
    }

    // protected function _prepareMassaction()
    // {
    //     $this->setMassactionIdField('id');
    //     $this->getMassactionBlock()->setFormFieldName('tabs');
    //     $this->_prepareMassactionColumn();
    //     // \Zend_Debug::dump($this->getMassactionBlock()->isAvailable());
    //     return parent::_prepareMassaction();
    // }

}
