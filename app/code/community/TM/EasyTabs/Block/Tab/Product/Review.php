<?php

class TM_EasyTabs_Block_Tab_Product_Review extends Mage_Review_Block_Product_View_List
{
    protected function _prepareLayout()
    {
        $reviewForm = $this->getLayout()->createBlock('review/form', 'product.review.form');
        if ($reviewForm) {
            $wrapper = $this->getLayout()
                ->createBlock('page/html_wrapper', 'product.review.form.fields.before');
            if ($wrapper) {
                $wrapper->setMayBeInvisible(1);
                $reviewForm->setChild('form_fields_before', $wrapper);
            }
            $this->setChild('review_form', $reviewForm);
        }
        return parent::_prepareLayout();
    }
}
