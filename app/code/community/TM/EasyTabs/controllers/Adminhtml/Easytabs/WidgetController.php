<?php

class TM_EasyTabs_Adminhtml_Easytabs_WidgetController
    extends TM_EasyTabs_Controller_Adminhtml_Abstract
{

    /**
     * Chooser Source action
     */
    public function indexAction()
    {
        $this->_title($this->__('TM'))
            ->_title($this->__('EasyTabs'))
            ->_title($this->__('Widget Tabs'));
        parent::indexAction();
    }

    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('easytabs/adminhtml_widget_chooser', '', array(
            'id' => $uniqId,
        ));
        $html = $pagesGrid->toHtml();
        $filter = $this->getRequest()->getParam('filter');
        $limit = $this->getRequest()->getParam('limit');
        if (is_null($filter) && is_null($limit)) {
            $chosen = $this->getLayout()->createBlock('core/template')
                ->setTemplate('tm/easytabs/widget/chosen.phtml');
            $html = $chosen->toHtml() . $html;
        }
        $this->getResponse()->setBody($html);
    }

}
