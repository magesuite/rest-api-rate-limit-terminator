<?php

declare(strict_types=1);

namespace MageSuite\MagesuiteRestApiRateLimitTerminator\Test\Integration\Model\Quote\Model\QuoteRepository;

class ValidateFingerprintTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Quote\Api\Data\CartInterfaceFactory $cartFactory = null;
    protected ?\Magento\Quote\Api\CartRepositoryInterface $cartRepository = null;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->cartFactory = $objectManager->create(\Magento\Quote\Api\Data\CartInterfaceFactory::class);
        $this->cartRepository = $objectManager->create(\Magento\Quote\Api\CartRepositoryInterface::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store rest_api_rate_limit_terminator/checkout/enable_fingerprint_for_guests 1
     */
    public function testFingerprintGuestCartException()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage((string)__('Fingerprint mismatch. Please refresh the cart.'));

        $cart1 = $this->cartFactory->create();
        $this->cartRepository->save($cart1);
        $cart2 = $this->cartFactory->create();
        $this->cartRepository->save($cart2);

        $this->assertEquals($cart1->getFingerprint(), $cart2->getFingerprint());
        $this->assertNotEmpty($cart1->getFingerprint());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store rest_api_rate_limit_terminator/checkout/enable_fingerprint_for_guests 0
     */
    public function testFingerprintGuestCartNonException()
    {
        $cart1 = $this->cartFactory->create();
        $this->cartRepository->save($cart1);
        $cart2 = $this->cartFactory->create();
        $this->cartRepository->save($cart2);

        $this->assertEquals($cart1->getFingerprint(), $cart2->getFingerprint());
        $this->assertEmpty($cart1->getFingerprint());
    }
}
