<?php

class TM_EasyTabs_Model_Config extends Varien_Object
{
    protected $_lastSavedId;

    public function getCollection()
    {
        $collection = new TM_EasyTabs_Model_Config_Collection();
        $collection->load();
        return $collection;
    }

    public function load($idValue)
    {
        foreach ($this->getCollection() as $item) {
            if ($item->getId() == $idValue) {
                $this->setData($item->getData());
            }

        }
        return $this;
    }

    protected function _getItems()
    {
        return $this->getCollection()->getRawItems();
    }

    protected function _save($items)
    {
        $section  = 'tm_easytabs';
        $websiteId = $storeId = 0;

        $groups = array(
            'general' => array(
                'fields' => array(
                      'config' => array(
                          'value' => json_encode($items)
        ))));
        Mage::getSingleton('adminhtml/config_data')
            ->setSection($section)
            ->setWebsite($websiteId)
            ->setStore($storeId)
            ->setGroups($groups)
            ->save();

        Mage::getConfig()->reinit();
        Mage::dispatchEvent('admin_system_config_section_save_after', array(
            'website' => $websiteId,
            'store'   => $storeId,
            'section' => $section
        ));
        Mage::app()->reinitStores();

        // website and store codes can be used in event implementation, so set them as well
        Mage::dispatchEvent("admin_system_config_changed_section_{$section}",
            array('website' => $websiteId, 'store' => $storeId)
        );
    }

    public function getAlias()
    {
        $alias = $this->getData('alias');
        if (empty($alias)) {
            $alias = str_replace(' ', '_', strtolower(trim($this->getTitle())));
            $this->setData('alias', $alias);
        }
        return $alias;
    }

    public function save()
    {
        $items = $this->_getItems();

        $id = (int) $this->getId();
        if (!$id) {
            $id = max(array_keys($items)) + 1;
            $this->unsId();
        }

        $items[$id] = $this->getData();
        $items[$id]['id'] = $id;
        $this->_lastSavedId = $id;
        $this->_save($items);

        return $this;
    }

    public function getLastSavedId()
    {
        return $this->_lastSavedId;
    }

    public function delete()
    {
        $items = $this->_getItems();

        $id = (int) $this->getId();
        if ($id) {
            unset($items[$id]);
        }
        $this->_save($items);
        return $this;
    }
}