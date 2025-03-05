<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Block\Adminhtml\Form\Field;

class SoapBlockedServices extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected function _prepareToRender(): void
    {
        $this->addColumn('service_url', ['label' => __('Service Method Name'), 'class' => 'required-entry']);
        $this->addColumn('service_acl', ['label' => __('Service ACL')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
