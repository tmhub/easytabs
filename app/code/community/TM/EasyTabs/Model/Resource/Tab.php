<?php

class TM_EasyTabs_Model_Resource_Tab
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('easytabs/tab', 'id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

        if ($object->getBlockType() && !$object->getBlock()) {
            $object->setBlock($object->getBlockType());
        }

        return parent::_beforeSave($object);

    }


    /**
     * Process tab data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return TM_EasyTabs_Model_Resource_Tab
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'tab_id = ?'     => (int) $object->getId(),
        );
        $this->_getWriteAdapter()->delete($this->getTable('easytabs/store'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * Assign tab to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return TM_EasyTabs_Model_Resource_Tab
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {

        //  1. SAVE STORES FOR TAB
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table  = $this->getTable('easytabs/store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = array(
                'tab_id = ?'     => (int) $object->getId(),
                'store_id IN (?)' => $delete
            );
            $this->_getWriteAdapter()->delete($table, $where);
        }
        if ($insert) {
            $data = array();
            foreach ($insert as $storeId) {
                try {
                    $store = Mage::app()->getStore($storeId);
                    $data[] = array(
                        'tab_id'  => (int) $object->getId(),
                        'store_id' => (int) $store->getId()
                    );
                }
                catch (Exception $e) {
                    // ignore id if there are no such store
                }
            }
            if (!empty($data)) {
                $this->_getWriteAdapter()->insertMultiple($table, $data);
            }
        }

        //Mark layout cache as invalidated
        Mage::app()->getCacheInstance()->invalidateType('layout');

        return parent::_afterSave($object);

    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return TM_EasyTabs_Model_Resource_Tab
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            // get stores assigned to deal
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }
        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($tabId)
    {
        $adapter = $this->_getReadAdapter();
        $select  = $adapter->select()
            ->from($this->getTable('easytabs/store'), 'store_id')
            ->where('tab_id = ?', (int)$tabId);
        return $adapter->fetchCol($select);
    }

}
