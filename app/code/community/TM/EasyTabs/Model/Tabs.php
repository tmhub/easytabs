<?php

class TM_EasyTabs_Model_Tabs extends Mage_Widget_Model_Widget
{
    /**
     * Load Easytabs XML config from easytabs.xml files and cache it
     *
     * @return Varien_Simplexml_Config
     */
    public function getXmlConfig()
    {
        $cachedXml = Mage::app()->loadCache('easytabs_config');
        if ($cachedXml) {
            $xmlConfig = new Varien_Simplexml_Config($cachedXml);
        } else {
            $config = new Varien_Simplexml_Config();
            $config->loadString('<?xml version="1.0"?><easytabs></easytabs>');
            Mage::getConfig()->loadModulesConfiguration('easytabs.xml', $config);
            $xmlConfig = $config;
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($config->getXmlString(), 'easytabs_config',
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
        return $xmlConfig;
    }

    public function getTabsArray($filters = array())
    {
        return parent::getWidgetsArray($filters);
    }
}