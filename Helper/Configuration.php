<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected const ENABLE_FINGERPRINT_FOR_GUESTS_XML_PATH = 'rest_api_rate_limit_terminator/checkout/enable_fingerprint_for_guests';
    protected const FINGERPRINT_TTL_XML_PATH = 'rest_api_rate_limit_terminator/checkout/fingerprint_ttl';
    protected const GUESTS_CART_ITEMS_LIMIT_XML_PATH = 'rest_api_rate_limit_terminator/checkout/guests_cart_items_limit';
    protected const AUTHORIZED_CART_ITEMS_LIMIT_XML_PATH = 'rest_api_rate_limit_terminator/checkout/authorized_cart_items_limit';
    protected const REST_BLOCKED_SERVICES_XML_PATH = 'rest_api_rate_limit_terminator/blocked_services/rest';
    protected const SOAP_BLOCKED_SERVICES_XML_PATH = 'rest_api_rate_limit_terminator/blocked_services/soap';

    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->serializer = $serializer;

        parent::__construct($context);
    }

    public function isFingerprintForGuestsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_FINGERPRINT_FOR_GUESTS_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getFingerprintTtl(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::FINGERPRINT_TTL_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCartItemsLimit(bool $isAuthorized)
    {
        return $isAuthorized ? $this->getAuthorizedCartItemsLimit() : $this->getGuestsCartItemsLimit();
    }

    public function getGuestsCartItemsLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::GUESTS_CART_ITEMS_LIMIT_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getAuthorizedCartItemsLimit(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::AUTHORIZED_CART_ITEMS_LIMIT_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getRestBlockedServicesList(): array
    {
        $blockedServices = $this->scopeConfig->getValue(
            self::REST_BLOCKED_SERVICES_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (is_string($blockedServices)) {
            $blockedServices = $this->serializer->unserialize($blockedServices);
        }

        return $blockedServices ?: [];
    }

    public function getSoapBlockedServicesList(): array
    {
        $blockedServices = $this->scopeConfig->getValue(
            self::SOAP_BLOCKED_SERVICES_XML_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (is_string($blockedServices)) {
            $blockedServices = $this->serializer->unserialize($blockedServices);
        }

        return $blockedServices ?: [];
    }
}
