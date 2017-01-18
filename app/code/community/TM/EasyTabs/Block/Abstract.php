<?php

abstract class TM_EasyTabs_Block_Abstract extends Mage_Core_Block_Template
{
    protected $_tabs = array();

    protected function _getCollection()
    {
        $collection = Mage::getModel('easytabs/tab')->getCollection();
        $storeId    = Mage::app()->getStore()->getStoreId();
        return $collection
            ->addStoreFilter($storeId)
            ->addFieldToFilter('status', array('eq' => 1))
            ->setOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC);
    }

    protected function _prepareLayout()
    {
        if (!Mage::getStoreConfig('tm_easytabs/general/enabled')) {
            return parent::_prepareLayout();
        }

        $collection = $this->_getCollection();

        $filterTabs = $this->getFilterTabs();
        if (!empty($filterTabs)) {
            $filterTabs = str_replace(' ', '', $filterTabs);
            $filterTabs = explode(',', $filterTabs);
            $collection->addFieldToFilter('alias', array('in' => $filterTabs));
        }

        foreach ($collection as $tab) {
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
    public function addTab($alias, $title, $block = false, $template = false, $attributes = array())
    {
        if (!$title || ($block && $block !== 'easytabs/tab_html' && !$template)) {
            return false;
        }

        if (!$block) {
            $block = $this->getLayout()->getBlock($alias);
            if (!$block) {
                return false;
            }
        } else {
            // if (!Mage::registry('product') && strstr($block, 'product')) {
            //     return false;
            // }

            $block = $this->getLayout()
                ->createBlock($block, $alias, $attributes)
                ->setTemplate($template);
        }

        $tab = array(
            'alias' => $alias,
            'title' => $title
        );

        if (isset($attributes['sort_order'])) {
            $tab['sort_order'] = $attributes['sort_order'];
        }

        $this->_tabs[] = $tab;

        $this->setChild($alias, $block);
    }

    protected function _sort($tab1, $tab2)
    {
        if (!isset($tab2['sort_order'])) {
            return -1;
        }

        if (!isset($tab1['sort_order'])) {
            return 1;
        }

        if ($tab1['sort_order'] == $tab2['sort_order']) {
            return 0;
        }
        return ($tab1['sort_order'] < $tab2['sort_order']) ? -1 : 1;
    }

    public function getTabs()
    {
        usort($this->_tabs, array($this, '_sort'));
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
    public function getShowAnchor()
    {
        return Mage::getStoreConfigFlag('tm_easytabs/general/show_anchor');
    }

}
