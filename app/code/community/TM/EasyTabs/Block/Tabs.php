<?php

class TM_EasyTabs_Block_Tabs extends Mage_Core_Block_Template
{
    protected $_tabs = array();

    protected function _getCollection()
    {
        $collection = new TM_EasyTabs_Model_Config_Collection();
        $storeId    = Mage::app()->getStore()->getStoreId();
        return $collection
            ->setOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC)
            ->addFieldToFilter('status', array('eq' => 1))
            ->addFieldToFilter('store_id', array('in' => array($storeId, 0)));
    }

    protected function _prepareLayout()
    {
        if (!Mage::getStoreConfig('tm_easytabs/general/enabled')) {
            return parent::_prepareLayout();
        }
        foreach ($this->_getCollection() as $tab) {
            $this->addTab(
                $tab->getAlias(),
                $tab->getTitle(),
                $tab->getBlock(),
                $tab->getTemplate(),
                $tab->getData()
            );

            $unsets = (string) $tab->getUnset();
            $unsets = explode(',', $unsets);
            foreach ($unsets as $unset) {
                if (false === strpos($unset, '::')) {
                    continue;
                }
                list($blockName, $alias) = explode('::', $unset);
                $block = $this->getLayout()->getBlock($blockName);
                if ($block) {
                    /**
                     * @see http://www.magentocommerce.com/bug-tracking/issue/index/id/142
                     * Call sortChildren before unset to fix Magento bug in
                     *     Mage_Core_Block_Abstract::sortChildren:
                     *  1. Unset drop the key from the _sortedChildren array
                     *  2. sortChildren method finds the block index to remove
                     *  3. sortChildren method uses array_splice wich reorder array
                     *      and previously founded block index become incorrect.
                     *
                     * The fix is works because sort is called only once.
                     * The correct way is to add the following line to
                     *     Mage_Core_Block_Abstract::unsetChild after
                     *     `unset($this->_sortedChildren[$key]);`:
                     *
                     *  $this->_sortedChildren = array_values($this->_sortedChildren);
                     */
                    $block->sortChildren();
                    $block->unsetChild($alias);
                }
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * @param string $title
     * @param string $block
     * @param string $template
     * @param array  $attributes
     */
    public function addTab($alias, $title, $block, $template, $attributes = array())
    {
        if (!$title || !$block || ($block !== 'easytabs/tab_html' && !$template)) {
            return false;
        }

        $this->_tabs[] = array(
            'alias' => $alias,
            'title' => $title
        );

        $this->setChild($alias,
            $this->getLayout()
                ->createBlock($block, $alias, $attributes)
                ->setTemplate($template)
        );
    }

    public function getTabs()
    {
        return $this->_tabs;
    }

    /**
     * Check tab content for anything except html tags and spaces
     *
     * @param  string  $content
     * @return boolean
     */
    public function isEmptyString($content)
    {
        $content = strip_tags(
            $content,
            '<hr><img><iframe><embed><object><video><audio><input><textarea><script><style><link><meta>'
        );
        $content = trim($content);
        return strlen($content) === 0;
    }

    public function getTabTitle($tab)
    {
        if (!strstr($tab['title'], '{{') || !strstr($tab['title'], '}}')) {
            return $tab['title'];
        }
        $scope = $this->getChild($tab['alias']);
        /** @var TM_EasyTabs_Model_Template_Filter $processor **/
        $processor = Mage::getModel('easytabs/template_filter')
            ->setScope($scope);

        return $processor->filter($tab['title']);
    }

    /**
     * Returns show anchor flag
     *
     * @return boolean
     */
    public function canShowAnchor()
    {
        return Mage::getStoreConfigFlag('tm_easytabs/general/show_anchor');
    }
}
