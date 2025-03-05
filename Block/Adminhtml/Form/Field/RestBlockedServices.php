<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Block\Adminhtml\Form\Field;

class RestBlockedServices extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $httpMethodRenderer;

    protected function _prepareToRender(): void
    {
        $this->addColumn('service_url', ['label' => __('Service URL'), 'class' => 'required-entry']);
        $this->addColumn('http_method', [
            'label' => __('HTTP Method'),
            'renderer' => $this->getHttpMethodRenderer()
        ]);
        $this->addColumn('service_acl', ['label' => __('Service ACL')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row): void
    {
        $options = [];
        $method = $row->getData('http_method');
        if ($method !== null) {
            $options['option_' . $this->getHttpMethodRenderer()->calcOptionHash($method)] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    private function getHttpMethodRenderer(): \Magento\Framework\View\Element\BlockInterface
    {
        if (!$this->httpMethodRenderer) {
            $this->httpMethodRenderer = $this->getLayout()->createBlock(
                HttpMethodColumn::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->httpMethodRenderer;
    }
}
