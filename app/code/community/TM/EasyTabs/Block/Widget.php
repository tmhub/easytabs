<?php

class TM_EasyTabs_Block_Widget extends TM_EasyTabs_Block_Abstract implements Mage_Widget_Block_Interface
{

    protected function _getCollection()
    {
        $collection = parent::_getCollection()->addWidgetTabFilter();
        $filterTabs = $this->getFilterTabs();
        if (!empty($filterTabs)) {
            $filterTabs = str_replace(' ', '', $filterTabs);
            $filterTabs = explode(',', $filterTabs);
            $collection->addFieldToFilter('alias', array('in' => $filterTabs));
        }
        return $collection;
    }

    /**
     * Returns show anchor flag
     *
     * @return boolean
     */
    public function getUpdateUrlHash()
    {
        return 0;
    }

    public function getHtmlId()
    {
        if (!$this->getData('html_id')) {
            $htmlId = 'easytabs-widget-';
            foreach ($this->_getCollection() as $tab) {
                $htmlId .= $tab->getId();
            }
            $this->setData('html_id', $htmlId);
        }
        return $this->getData('html_id');
    }

}
