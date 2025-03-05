<?php

declare(strict_types=1);

namespace MageSuite\MagesuiteRestApiRateLimitTerminator\Plugin\Checkout\Model\Cart;

class LimitCartItemsTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository = null;
    protected ?\Magento\Checkout\Model\CartFactory $cartFactory = null;
    protected ?\Magento\Customer\Model\Customer $customer = null;
    protected ?\Magento\Customer\Model\Session $customerSession = null;

    public function setUp(): void
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->cartFactory = $objectManager->create(\Magento\Checkout\Model\CartFactory::class);
        $this->customer = $objectManager->create(\Magento\Customer\Model\Customer::class);
        $this->customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        $this->productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Checkout/_files/products.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store rest_api_rate_limit_terminator/checkout/guests_cart_items_limit 2
     */
    public function testAddDifferentProductsToCartForGuest()
    {
        $cart = $this->cartFactory->create();
        $products = [
            $this->productRepository->get('Simple Product 1 sku'),
            $this->productRepository->get('Simple Product 2 sku'),
            $this->productRepository->get('Simple Product 3 sku')
        ];

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage((string)__('You cannot add more than %1 items to the cart.', 2));

        foreach ($products as $product) {
            $cart->addProduct($product, ['qty' => 1, 'product' => $product->getId()]);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Checkout/_files/products.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store rest_api_rate_limit_terminator/checkout/authorized_cart_items_limit 2
     */
    public function testAddDifferentProductsToCartForAuthorizedCustomer()
    {
        $customer = $this->customer->load(1);
        $this->customerSession->setCustomerAsLoggedIn($customer);

        $cart = $this->cartFactory->create();
        $products = [
            $this->productRepository->get('Simple Product 1 sku'),
            $this->productRepository->get('Simple Product 2 sku'),
            $this->productRepository->get('Simple Product 3 sku')
        ];

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage((string)__('You cannot add more than %1 items to the cart.', 2));

        foreach ($products as $product) {
            $cart->addProduct($product, ['qty' => 1, 'product' => $product->getId()]);
        }
    }
}
