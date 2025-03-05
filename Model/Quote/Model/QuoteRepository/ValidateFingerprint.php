<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Model\Quote\Model\QuoteRepository;

class ValidateFingerprint extends AbstractPlugin
{
    public function aroundSave( // phpcs:ignore
        \Magento\Quote\Api\CartRepositoryInterface $subject,
        callable $proceed,
        \Magento\Quote\Api\Data\CartInterface $cart
    ) {
        if (!$this->configuration->isFingerprintForGuestsEnabled()) {
            return $proceed($cart);
        }

        if ($cart->getId() || $cart->getCustomerId()) {
            return $proceed($cart);
        }

        if (!$this->fingerprintManager->validate($cart)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Fingerprint mismatch. Please refresh the cart.'));
        }

        return $proceed($cart);
    }
}
