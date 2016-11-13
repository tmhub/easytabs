<?php

    $tabsData = json_decode(
            Mage::getStoreConfig('tm_easytabs/general/config'),
            true
        );

    foreach ($tabsData as $data) {
        $tab = Mage::getModel('easytabs/tab');
        $tab->setData($data);
        $tab->setProductTab(1);
        $tab->unsetData('id');
        $tab->save();
    }
