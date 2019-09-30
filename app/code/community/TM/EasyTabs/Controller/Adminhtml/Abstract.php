<?php

abstract class TM_EasyTabs_Controller_Adminhtml_Abstract
    extends Mage_Adminhtml_Controller_Action
{

    protected $productTab = 0;

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('templates_master/easytabs')
            ->_addBreadcrumb(
                Mage::helper('easytabs')->__('easytabs'),
                Mage::helper('easytabs')->__('easytabs')
            );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('easytabs/tab');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('easytabs')
                        ->__('This tab no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        } else {
            // set flag `product tab`
            $model->setProductTab($this->productTab);
        }

        if ($model->getId()) {
            $this->_title(Mage::helper('easytabs')->__('Edit Tab'));
        } else {
            $this->_title(Mage::helper('easytabs')->__('New Tab'));
        }

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('easytabs_tab', $model);


        $this->_initAction();
        $this->renderLayout();
    }

    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            //init model and set data
            $model = Mage::getModel('easytabs/tab');
            if ($id = $this->getRequest()->getParam('id')) {
                $model->load($id);
            }

            // $model->setData($data);
            $model->loadPost($data);
            foreach ($data['parameters'] as $key => $value) {
                $model->setData($key, $value);
            }

            // try to save blacklist rule
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('easytabs')->__(
                        'Tab \'%s\' has been saved.',
                        $model->getTitle()
                    )
                );
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit',
                        array(
                            'id' => $model->getId(),
                            '_current'=>true
                        )
                    );
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    $this->__('An error occurred while saving.')
                );
            }
            $this->_getSession()->setFormData($data);
            $this->_redirect(
                '*/*/edit',
                array('id' => $this->getRequest()->getParam('id'))
            );
            return;
        }
        $this->_redirect('*/*/');
    }

    /**
     * Overriden Mage_Adminhtml_Catalog_CategoryController::wysiwygAction
     * Changed block type to allow to use widgets
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', sha1(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'tmcore/adminhtml_widget_form_element_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => $storeId,
                'store_media_url'   => $storeMediaUrl,
            )
        );

        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('easytabs/tab');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Tab \'%s\' has been deleted.', $model->getTitle()));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a tab to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function loadTabOptionsAction()
    {
        try {
            $this->loadLayout('empty');
            if ($paramsJson = $this->getRequest()->getParam('widget')) {
                $request = Mage::helper('core')->jsonDecode($paramsJson);
                if (is_array($request)) {
                    // $layout = $this->getLayout();
                    // print_r((array_keys($layout->getAllBlocks())));die;
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

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('easytabs');
        if (!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getModel('easytabs/tab')->load($id);
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
                    $model = Mage::getModel('easytabs/tab')->load($id);
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
            ->setRule(Mage::getModel('easytabs/tab', array('product_tab' => $this->productTab)))
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

    protected function _isAllowed()
    {
        $tabsType = $this->productTab ? 'product_tabs' : 'widget_tabs';
        return Mage::getSingleton('admin/session')
            ->isAllowed('templates_master/easytabs/' . $tabsType);
    }

}
