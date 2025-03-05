<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Block\Adminhtml\Form\Field;

class HttpMethodColumn extends \Magento\Framework\View\Element\Html\Select
{
    protected function _construct()
    {
        parent::_construct();

        $this->setOptions([
            ['label' => 'POST', 'value' => 'POST'],
            ['label' => 'GET', 'value' => 'GET'],
            ['label' => 'DELETE', 'value' => 'DELETE'],
            ['label' => 'PUT', 'value' => 'PUT'],
            ['label' => 'OPTION', 'value' => 'OPTION'],
        ]);
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function setInputId($value)
    {
        return $this->setId($value);
    }
}
