<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Model\Quote\Model\QuoteRepository;

class SetFingerprint extends AbstractPlugin
{
    public function beforeSave(
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        \Magento\Quote\Api\Data\CartInterface $cart
    ): array {
        if ($cart->getCustomerId() || !$this->configuration->isFingerprintForGuestsEnabled()) {
            return [$cart];
        }

        $fingerprint = $this->fingerprintManager->getFingerprint();
        $cart->setFingerprint($fingerprint);

        return [$cart];
    }
}
