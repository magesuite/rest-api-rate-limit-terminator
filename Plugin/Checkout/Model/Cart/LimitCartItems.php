<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Plugin\Checkout\Model\Cart;

class LimitCartItems
{
    protected \Magento\Customer\Model\Session $customerSession;
    protected \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration;

    public function __construct(
        \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->configuration = $configuration;
        $this->customerSession = $customerSession;
    }

    public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $subject,
        \Magento\Catalog\Model\Product $product,
        $requestInfo = null
    ) {
        $quote = $subject->getQuote();
        $itemsCount = $this->getItemsCount($quote);
        $limit = $this->configuration->getCartItemsLimit((bool)$this->customerSession->getCustomerId());

        if ($itemsCount >= $limit) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You cannot add more than %1 items to the cart.', $limit)
            );
        }

        return [$product, $requestInfo];
    }

    protected function getItemsCount(\Magento\Quote\Api\Data\CartInterface $quote):int {
        $itemsCount = 0;
        $items = $quote->getAllItems();

        if(empty($items)) {
            return 0;
        }

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            $itemsCount++;
        }

        return $itemsCount;
    }
}
