<?php

class TM_EasyTabs_Adminhtml_Easytabs_IndexController extends Mage_Adminhtml_Controller_Action
{
     protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('easytabs')
            ->_addBreadcrumb(
                Mage::helper('easytabs')->__('easytabs'),
                Mage::helper('easytabs')->__('easytabs')
            );

        return $this;
    }

    protected function _getCollection()
    {
        return new TM_EasyTabs_Model_Config_Collection();
    }

    public function indexAction()
    {
        Mage::register('easytabs_collection', $this->_getCollection());
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('easytabs/config')->load($id);
        Mage::register('easytabs_tab_data', $model);
        $this->_initAction();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $params = $this->getRequest()->getPost();
            unset($params['form_key']);
            if (empty($params['block']) && !empty($params['block_type'])) {
                $params['block'] = $params['block_type'];
            }
            unset($params['block_type']);
            if (isset($params['parameters'])) {
                $params = array_merge($params['parameters'], $params);
                unset($params['parameters']);
            }
            $model = Mage::getModel('easytabs/config')
                ->setData($params)
                ->save();

            $session->addSuccess(Mage::helper('adminhtml')->__(
                'The configuration has been saved.'
            ));

            if ($this->getRequest()->getParam('back') && $model->getLastSavedId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getLastSavedId()));
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (Mage_Core_Exception $e) {
            foreach(explode("\n", $e->getMessage()) as $message) {
                $session->addError($message);
            }
        } catch (Exception $e) {
            $session->addException($e,
                Mage::helper('adminhtml')->__(
                    'An error occurred while saving this configuration:'
                )
                . ' ' . $e->getMessage()
            );
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0 ) {
            try {
                $id = $this->getRequest()->getParam('id');
                $model = Mage::getModel('easytabs/config');
                $model->load($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('easytabs');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('easytabs/config')->load($id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam('easytabs');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $status = $this->getRequest()->getParam('status');
                foreach ($ids as $id) {
                    $model = Mage::getModel('easytabs/config')->load($id);
                    $model->setStatus($status)
                        ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function loadTabOptionsAction()
    {
        try {
            $this->loadLayout('empty');
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = Mage::helper('core')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    $optionsBlock = $this->getLayout()->getBlock('easytabs.tab.options');
                    if (isset($request['widget_type'])) {
                        $optionsBlock->setWidgetType($request['widget_type']);
                    }
                    if (isset($request['values'])) {
                        $optionsBlock->setWidgetValues($request['values']);
                    }
                }
                $this->renderLayout();
            }
        } catch (Mage_Core_Exception $e) {
            $result = array('error' => true, 'message' => $e->getMessage());
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
}
