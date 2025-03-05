<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Plugin\Checkout\Model\Session;

class ValidateFingerprint
{
    protected \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration;
    protected \MageSuite\RestApiRateLimitTerminator\Service\FingerprintManager $fingerprintManager;

    public function __construct(
        \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration,
        \MageSuite\RestApiRateLimitTerminator\Service\FingerprintManager $fingerprintManager
    ) {
        $this->configuration = $configuration;
        $this->fingerprintManager = $fingerprintManager;
    }

    public function afterGetQuote(\Magento\Checkout\Model\Session $subject, \Magento\Quote\Model\Quote $result): \Magento\Quote\Model\Quote
    {
        if (!$this->configuration->isFingerprintForGuestsEnabled()) {
            return $result;
        }

        if ($result->getCustomerId() || $result->getFingerprint()) {
            return $result;
        }

        $fingerprint = $this->fingerprintManager->getFingerprint();
        $result->setFingerprint($fingerprint);

        if (!$this->fingerprintManager->validate($result)) {
            $result->unsFingerprint();
            throw new \Magento\Framework\Exception\LocalizedException(__('Fingerprint mismatch. Please refresh the cart.'));
        }

        return $result;
    }
}
