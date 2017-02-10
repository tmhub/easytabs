<?php

    $str = <<<TABDATA
{
    "1":{
      "title":"Product Description",
      "alias":"description_tabbed",
      "block":"easytabs\/tab_product_description",
      "custom_option":null,
      "template":"tm\/easytabs\/tab\/catalog\/product\/view\/description.phtml",
      "unset":"product.info::description",
      "sort_order":1,
      "status":true,
      "store_id":"0"
    },
    "2":{
      "title":"Additional Information",
      "alias":"additional_tabbed",
      "block":"easytabs\/tab_product_additional",
      "custom_option":null,
      "template":"tm\/easytabs\/tab\/catalog\/product\/view\/attributes.phtml",
      "unset":"product.info::additional",
      "sort_order":2,
      "status":true,
      "store_id":"0"
    },
    "3":{
      "title":"We Also Recommend",
      "alias":"upsell_products_tabbed",
      "block":"easytabs\/tab_product_upsell",
      "custom_option":null,
      "template":"catalog\/product\/list\/upsell.phtml",
      "unset":"product.info::upsell_products",
      "sort_order":4,
      "status":true,
      "store_id":"0"
    },
    "4":{
      "title":"Related Products",
      "alias":"related_tabbed",
      "block":"easytabs\/tab_product_related",
      "custom_option":null,
      "template":"tm\/easytabs\/tab\/catalog\/product\/related.phtml",
      "unset":"right::catalog.product.related,product.info::related_products",
      "sort_order":5,
      "status":true,
      "store_id":"0"
    },
    "5":{
      "title":"Tags&nbsp;({{eval code=\"getCount()\"}})",
      "alias":"tags_tabbed",
      "block":"easytabs\/tab_product_tags",
      "custom_option":null,
      "template":"tm\/easytabs\/tab\/tag\/product\/list.phtml",
      "unset":"product.info.additional::product_tag_list",
      "sort_order":6,
      "status":true,
      "store_id": "0"
    },
    "6":{
      "title":"Reviews&nbsp;({{eval code=\"getReviewsCollection()->count()\"}})",
      "alias":"review_tabbed",
      "block":"easytabs\/tab_product_review",
      "custom_option":null,
      "template":"tm\/easytabs\/tab\/review\/product\/view\/list.phtml",
      "unset":"product.info::reviews",
      "sort_order":7,
      "status":true,
      "store_id": "0"
    },
    "7":{
      "title":"electronics-landing",
      "alias":"cms",
      "block":"easytabs\/tab_cms",
      "custom_option":"electronics-landing",
      "template":"tm\/easytabs\/tab\/cms.phtml",
      "unset":"",
      "sort_order":8,
      "status":false,
      "store_id":"0"
    },
    "8":{
      "title":"Color Attribute",
      "alias":"attribute",
      "block":"easytabs\/tab_attribute",
      "custom_option":"color",
      "template":"tm\/easytabs\/tab\/catalog\/product\/attribute.phtml",
      "unset":"",
      "sort_order":9,
      "status":false,
      "store_id":"0"
    },
    "9":{
      "title":"Recurring Profile",
      "alias":"recurring_info",
      "block":"easytabs\/tab_template",
      "custom_option":"payment\/catalog_product_view_profile",
      "template":"payment\/catalog\/product\/view\/profile\/schedule.phtml",
      "unset":"product.info::recurring_info",
      "sort_order":10,
      "status":true,
      "store_id":"0"
    }
}
TABDATA;

    if (Mage::getStoreConfig('tm_easytabs/general/config')) {
        $str = Mage::getStoreConfig('tm_easytabs/general/config');
    }

    $tabsData = json_decode($str, true);

    foreach ($tabsData as $data) {
        $tab = Mage::getModel('easytabs/tab');
        $tab->setData($data);
        $tab->setProductTab(1);
        $tab->unsetData('id');
        $tab->save();
    }
