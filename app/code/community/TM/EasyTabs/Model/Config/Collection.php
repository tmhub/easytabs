<?php

class TM_EasyTabs_Model_Config_Collection extends Varien_Data_Collection
{
    protected $_itemObjectClass = 'TM_EasyTabs_Model_Config';

    protected $_rawItems = array();

    /**
     * Filter rendering helper variables
     *
     * @see Varien_Data_Collection::$_filter
     * @see Varien_Data_Collection::$_isFiltersRendered
     */
    private $_filterIncrement    = 0;
    private $_filterBrackets     = array();
    private $_filterEvalRendered = '';

    /**
     * Lauch data collecting
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Varien_Data_Collection_Filesystem
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        $configOption = Mage::getStoreConfig('easy_tabs/general/config', 0);
//        Zend_Debug::dump($configOption); die;
        $this->_rawItems = json_decode($configOption, true);
//        Zend_Debug::dump($this->_rawItems);
//        Zend_Debug::dump($this->_filters);
        $this->_filterAndSort();
        // calculate totals
        $this->_totalRecords = count($this->_rawItems);
        $this->_setIsLoaded();
        // paginate and add items
        $from = ($this->getCurPage() - 1) * $this->getPageSize();
        $to = $from + $this->getPageSize() - 1;
        $isPaginated = $this->getPageSize() > 0;
        $cnt = 0;
        foreach ($this->_rawItems as $row) {
            $cnt++;
            if ($isPaginated && ($cnt < $from || $cnt > $to)) {
                continue;
            }
            $item = new $this->_itemObjectClass();
            $this->addItem($item->addData($row));
            if (!$item->hasId()) {
                $item->setId($cnt);
            }
        }
        return $this;
    }

    public function getRawItems()
    {
        return $this->_rawItems;
    }

    /**
     * With specified collected items:
     *  - generate data
     *  - apply filters
     *  - sort
     *
     * @param string $attributeName '_collectedFiles' | '_collectedDirs'
     */
    private function _filterAndSort()
    {
        // apply filters on generated data
        if (!empty($this->_filters)) {
            foreach ($this->_rawItems as $key => $row) {
                if (!$this->_filterRow($row)) {
                    unset($this->_rawItems[$key]);
                }
            }
        }

        // sort (keys are lost!)
        if (!empty($this->_orders)) {
            usort($this->_rawItems, array($this, '_usort'));
        }
    }

    protected function _usort($a, $b)
    {
        foreach ($this->_orders as $key => $direction) {
            $result = $a[$key] > $b[$key] ? 1 : ($a[$key] < $b[$key] ? -1 : 0);
            return (self::SORT_ORDER_ASC === strtoupper($direction) ? $result : -$result);
            break;
        }
    }

    /**
     * Set select order
     * Currently supports only sorting by one column
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
//        Zend_Debug::dump(array($field => $direction));
//        die;
        $this->_orders = array($field => $direction);
        return $this;
    }

    /**
     * Set a custom filter with callback
     * The callback must take 3 params:
     *     string $field       - field key,
     *     mixed  $filterValue - value to filter by,
     *     array  $row         - a generated row (before generaring varien objects)
     *
     * @param string $field
     * @param mixed $value
     * @param string $type 'and'|'or'
     * @param callback $callback
     * @param bool $isInverted
     * @return Varien_Data_Collection_Filesystem
     */
    public function addCallbackFilter($field, $value, $type, $callback, $isInverted = false)
    {
        $this->_filters[$this->_filterIncrement] = array(
            'field'       => $field,
            'value'       => $value,
            'is_and'      => 'and' === $type,
            'callback'    => $callback,
            'is_inverted' => $isInverted
        );
        $this->_filterIncrement++;
        return $this;
    }

    /**
     * The filters renderer and caller
     * Aplies to each row, renders once.
     *
     * @param array $row
     * @return bool
     */
    protected function _filterRow($row)
    {
        // render filters once
        if (!$this->_isFiltersRendered) {
            $eval = '';
            for ($i = 0; $i < $this->_filterIncrement; $i++) {
                if (isset($this->_filterBrackets[$i])) {
                    $eval .= $this->_renderConditionBeforeFilterElement($i, $this->_filterBrackets[$i]['is_and'])
                        . $this->_filterBrackets[$i]['value'];
                }
                else {
                    $f = '$this->_filters[' . $i . ']';
                    $eval .= $this->_renderConditionBeforeFilterElement($i, $this->_filters[$i]['is_and'])
                        . ($this->_filters[$i]['is_inverted'] ? '!' : '')
                        . '$this->_invokeFilter(' . "{$f}['callback'], array({$f}['field'], {$f}['value'], " . '$row))';
                }
            }
            $this->_filterEvalRendered = $eval;
            $this->_isFiltersRendered = true;
        }
        $result = false;
        if ($this->_filterEvalRendered) {
            eval('$result = ' . $this->_filterEvalRendered . ';');
        }
        return $result;
    }

    /**
     * Invokes specified callback
     * Skips, if there is no filtered key in the row
     *
     * @param callback $callback
     * @param array $callbackParams
     * @return bool
     */
    protected function _invokeFilter($callback, $callbackParams)
    {
        list($field, $value, $row) = $callbackParams;
        if (!array_key_exists($field, $row)) {
            return false;
        }
        return call_user_func_array($callback, $callbackParams);
    }

    /**
     * Fancy field filter
     *
     * @param string $field
     * @param mixed $cond
     * @param string $type 'and' | 'or'
     * @see Varien_Data_Collection_Db::addFieldToFilter()
     * @return Varien_Data_Collection_Filesystem
     */
    public function addFieldToFilter($field, $cond, $type = 'and')
    {
        $inverted = true;

        // simply check whether equals
        if (!is_array($cond)) {
            return $this->addCallbackFilter($field, $cond, $type, array($this, 'filterCallbackEq'));
        }

        // versatile filters
        if (isset($cond['from']) || isset($cond['to'])) {
            $this->_addFilterBracket('(', 'and' === $type);
            if (isset($cond['from'])) {
                $this->addCallbackFilter($field, $cond['from'], 'and', array($this, 'filterCallbackIsLessThan'), $inverted);
            }
            if (isset($cond['to'])) {
                $this->addCallbackFilter($field, $cond['to'], 'and', array($this, 'filterCallbackIsMoreThan'), $inverted);
            }
            return $this->_addFilterBracket(')');
        }
        if (isset($cond['eq'])) {
            return $this->addCallbackFilter($field, $cond['eq'], $type, array($this, 'filterCallbackEq'));
        }
        if (isset($cond['neq'])) {
            return $this->addCallbackFilter($field, $cond['neq'], $type, array($this, 'filterCallbackEq'), $inverted);
        }
        if (isset($cond['like'])) {
            return $this->addCallbackFilter($field, $cond['like'], $type, array($this, 'filterCallbackLike'));
        }
        if (isset($cond['nlike'])) {
            return $this->addCallbackFilter($field, $cond['nlike'], $type, array($this, 'filterCallbackLike'), $inverted);
        }
        if (isset($cond['in'])) {
            return $this->addCallbackFilter($field, $cond['in'], $type, array($this, 'filterCallbackInArray'));
        }
        if (isset($cond['nin'])) {
            return $this->addCallbackFilter($field, $cond['nin'], $type, array($this, 'filterCallbackInArray'), $inverted);
        }
        if (isset($cond['notnull'])) {
            return $this->addCallbackFilter($field, $cond['notnull'], $type, array($this, 'filterCallbackIsNull'), $inverted);
        }
        if (isset($cond['null'])) {
            return $this->addCallbackFilter($field, $cond['null'], $type, array($this, 'filterCallbackIsNull'));
        }
        if (isset($cond['moreq'])) {
            return $this->addCallbackFilter($field, $cond['moreq'], $type, array($this, 'filterCallbackIsLessThan'), $inverted);
        }
        if (isset($cond['gt'])) {
            return $this->addCallbackFilter($field, $cond['gt'], $type, array($this, 'filterCallbackIsMoreThan'));
        }
        if (isset($cond['lt'])) {
            return $this->addCallbackFilter($field, $cond['lt'], $type, array($this, 'filterCallbackIsLessThan'));
        }
        if (isset($cond['gteq'])) {
            return $this->addCallbackFilter($field, $cond['gteq'], $type, array($this, 'filterCallbackIsLessThan'), $inverted);
        }
        if (isset($cond['lteq'])) {
            return $this->addCallbackFilter($field, $cond['lteq'], $type, array($this, 'filterCallbackIsMoreThan'), $inverted);
        }
        if (isset($cond['finset'])) {
            $filterValue = ($cond['finset'] ? explode(',', $cond['finset']) : array());
            return $this->addCallbackFilter($field, $filterValue, $type, array($this, 'filterCallbackInArray'));
        }

        // add OR recursively
        foreach ($cond as $orCond) {
            $this->_addFilterBracket('(', 'and' === $type);
            $this->addFieldToFilter($field, $orCond, 'or');
            $this->_addFilterBracket(')');
        }
        return $this;
    }

    /**
     * Prepare a bracket into filters
     *
     * @param string $bracket
     * @param bool $isAnd
     * @return Varien_Data_Collection_Filesystem
     */
    protected function _addFilterBracket($bracket = '(', $isAnd = true)
    {
        $this->_filterBrackets[$this->_filterIncrement] = array(
            'value' => $bracket === ')' ? ')' : '(',
            'is_and' => $isAnd,
        );
        $this->_filterIncrement++;
        return $this;
    }

    /**
     * Render condition sign before element, if required
     *
     * @param int $increment
     * @param bool $isAnd
     * @return string
     */
    protected function _renderConditionBeforeFilterElement($increment, $isAnd)
    {
        if (isset($this->_filterBrackets[$increment]) && ')' === $this->_filterBrackets[$increment]['value']) {
            return '';
        }
        $prevIncrement = $increment - 1;
        $prevBracket = false;
        if (isset($this->_filterBrackets[$prevIncrement])) {
            $prevBracket = $this->_filterBrackets[$prevIncrement]['value'];
        }
        if ($prevIncrement < 0 || $prevBracket === '(') {
            return '';
        }
        return ($isAnd ? ' && ' : ' || ');
    }

    /**
     * Does nothing. Intentionally disabled parent method
     *
     * @return Varien_Data_Collection_Filesystem
     */
    public function addFilter($field, $value, $type = 'and')
    {
        return $this;
    }

    /**
     * Callback method for 'like' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackLike($field, $filterValue, $row)
    {
        // removing single quotes, added by filter for mysql query: "'%FILTER_TEXT%'"
        $filterValue = preg_replace("/(^')(.*)('$)/", "$2", $filterValue);

        $filterValueRegex = str_replace('%', '(.*?)', preg_quote($filterValue, '/'));
        return (bool)preg_match("/^{$filterValueRegex}$/i", $row[$field]);
    }

    /**
     * Callback method for 'eq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackEq($field, $filterValue, $row)
    {
        return $filterValue == $row[$field];
    }

    /**
     * Callback method for 'in' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackInArray($field, $filterValue, $row)
    {
        $_value = $row[$field];
        if (!is_array($_value))  {
            $_value = array($_value);
        }
        return (bool)count(array_intersect($_value, $filterValue));
    }

    /**
     * Callback method for 'isnull' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsNull($field, $filterValue, $row)
    {
        return null === $row[$field];
    }

    /**
     * Callback method for 'moreq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsMoreThan($field, $filterValue, $row)
    {
        return $row[$field] > $filterValue;
    }

    /**
     * Callback method for 'lteq' fancy filter
     *
     * @param string $field
     * @param mixed $filterValue
     * @param array $row
     * @return bool
     * @see addFieldToFilter()
     * @see addCallbackFilter()
     */
    public function filterCallbackIsLessThan($field, $filterValue, $row)
    {
        return $row[$field] < $filterValue;
    }
}