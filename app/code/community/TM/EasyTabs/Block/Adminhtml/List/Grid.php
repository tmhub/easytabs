<?php

class TM_EasyTabs_Block_Adminhtml_List_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('easytabsGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('easytabs/tab')->getCollection();

        $blockList = $this->getLayout()->getBlock('easytabs_list');
        if ($blockList) {
            if ($blockList->getProductTab()) {
                $collection->addProductTabFilter();
            } else {
                $collection->addWidgetTabFilter();
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('easytabs')->__('ID'),
          'align'     =>'right',
          // 'width'     => '50px',
          'index'     => 'id',
          'type'      => 'number'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('easytabs')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('block', array(
            'header'    => Mage::helper('easytabs')->__('Block'),
            'align'     => 'left',
            'index'     => 'block',
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('easytabs')->__('Sort Order'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'sort_order',
            'type'      => 'number'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('easytabs')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('easytabs/config_status')->toOptionHash(),
        ));
        if (!Mage::app()->isSingleStoreMode()) {

            $this->addColumn('store_id', array(
                'header'     => Mage::helper('cms')->__('Store View'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_all'  => true,
                'store_view' => true,
                'sortable'   => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

//     protected function _prepareMassaction()
//     {
//         $this->setMassactionIdField('entity_id');
//         $this->getMassactionBlock()->setFormFieldName('easytabs');

//         $this->getMassactionBlock()->addItem('delete', array(
//              'label'    => Mage::helper('easytabs')->__('Delete'),
//              'url'      => $this->getUrl('*/*/massDelete'),
//              'confirm'  => Mage::helper('easytabs')->__('Are you sure?')
//         ));

//         $statuses = Mage::getSingleton('easytabs/config_status')->toOptionArray();

//         array_unshift($statuses, array('label'=>'', 'value'=>''));
// //        Zend_Debug::dump($statuses);die;
//         $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('easytabs')->__('Change status'),
//             'url' => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                 'visibility' => array(
//                     'name'   => 'status',
//                     'type'   => 'select',
//                     'class'  => 'required-entry',
//                     'label'  => Mage::helper('easytabs')->__('Status'),
//                     'values' => $statuses
//                 )
//             )
//         ));

//         return $this;
//     }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }
}