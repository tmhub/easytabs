<?php

class TM_EasyTabs_Adminhtml_Easytabs_ProductController
    extends TM_EasyTabs_Controller_Adminhtml_Abstract
{

    public function _construct()
    {
        // set flag `product tab`
        $this->productTab = 1;
        parent::_construct();
    }

    public function indexAction()
    {
        $this->_title($this->__('TM'))
            ->_title($this->__('Easy Tabs'))
            ->_title($this->__('Product Tabs'));
        parent::indexAction();
    }

    /**
     * New Condition HTML
     * @return [type] [description]
     */
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('easytabs/tab'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }
        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

}
