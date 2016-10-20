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
        $collection = Mage::registry('easytabs_collection');

        if (!$collection) {
            $collection = Mage::getModel('easytabs/tab')->getCollection();
            $collection->addCustomTabsFilter();
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('easytabs')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
          'type'      => 'number'
        ));
        $this->addColumn('title', array(
            'header'    => Mage::helper('easytabs')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));
//        $this->addColumn('alias', array(
//            'header'    => Mage::helper('easytabs')->__('Alias'),
//            'align'     => 'left',
//            'index'     => 'alias',
//        ));

        $this->addColumn('block', array(
            'header'    => Mage::helper('easytabs')->__('Block'),
            'align'     => 'left',
            'index'     => 'block',
        ));

        $this->addColumn('template', array(
            'header'    => Mage::helper('easytabs')->__('Template'),
            'align'     => 'left',
            'index'     => 'template',
            'width'      => 300,
        ));


        $this->addColumn('unset', array(
            'header'    => Mage::helper('easytabs')->__('Remove (reference::alias)'),
            'align'     => 'left',
            'index'     => 'unset',
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
//            $this->addColumn('website_id', array(
//                'header'    => Mage::helper('salesrule')->__('Website'),
//                'align'     =>'left',
//                'index'     => 'website_id',
//                'type'      => 'options',
//                'sortable'  => false,
//                'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
//                'width'     => 200,
//            ));

            $this->addColumn('store_id', array(
                'header'     => Mage::helper('catalog')->__('Store'),
                'index'      => 'store_id',
                'type'       => 'store',
                'store_all'  => true,
                'store_view' => true,
                'sortable'   => false,
                'width'      => 100,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

//        $this->addColumn('created_at', array(
//            'header'        => Mage::helper('easytabs')->__('Created date'),
//            'align'         => 'left',
//            'type'          => 'datetime',
//            'width'         => '100px',
//            'index'         => 'created_at',
//        ));
//
//        $this->addColumn('modified_at', array(
//            'header'        => Mage::helper('easytabs')->__('Modified date'),
//            'align'         => 'left',
//            'type'          => 'datetime',
//            'width'         => '100px',
//            'index'         => 'modified_at',
//        ));

//        $this->addExportType('*/*/exportCsv', Mage::helper('easytabs')->__('CSV'));
//        $this->addExportType('*/*/exportXml', Mage::helper('easytabs')->__('XML'));

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

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()
            ->addFieldToFilter('store_id', array('in' => array($value)));
    }
}