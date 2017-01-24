<?php

    // use Custom Block instead of Tab Product Related
    $tabsRelated = Mage::getModel('easytabs/tab')->getCollection()
        ->addProductTabFilter()
        ->addFilter('block', array('eq' => 'easytabs/tab_product_related'));

    foreach ($tabsRelated as $tab) {
        $tab->afterLoad();
        $tab->setBlock('easytabs/tab_template');
        $tab->setCustomOption('catalog/product_list_related');
        $tab->setTemplate('catalog/product/list/related.phtml');
        $tab->save();
    }
