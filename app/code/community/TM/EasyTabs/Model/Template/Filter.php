<?php

class TM_EasyTabs_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    protected $_scope;

    public function setScope($scope)
    {
        $this->_scope = $scope;
        return $this;
    }

    public function getScope()
    {
        return $this->_scope;
    }

    public function evalDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        if (isset($params['scope'])) {
            $scope = $params['scope'];
            $scope = Mage::app()->getLayout()->getBlock($scope);
            $this->setScope($scope);
        }

        $scope = $this->getScope();
        if (!$scope || !isset($params['code'])) {
            return '';
        }

        $methods = explode('->', str_replace(array('(', ')'), '', $params['code']));

        foreach ($methods as $method) {
            $callback = array($scope, $method);
            if(!is_callable($callback)) {
                continue;
            }
            
            try {
                $replacedValue = call_user_func($callback);
            } catch (Exception $e) {
                throw $e;
            }
            
            $scope = $replacedValue;
        }

        return (string) $replacedValue;
    }
}