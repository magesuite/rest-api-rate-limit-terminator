<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Service;

class FingerprintManager
{
    protected \Magento\Framework\App\Request\Http $request;
    protected \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress;
    protected \Magento\Framework\Serialize\SerializerInterface $serializer;
    protected \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configuration
    ) {
        $this->configuration = $configuration;
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
        $this->serializer = $serializer;
    }

    public function getFingerprint(): string
    {
        $data = [
            'user_agent' => $this->getUserAgent(),
            'ip_address' => $this->getRemoteAddress(),
            'referer' => $this->getReferer(),
        ];

        $jsonData = $this->serializer->serialize($data);

        return sha1($jsonData);
    }

    public function validate(\Magento\Quote\Api\Data\CartInterface $cart): bool
    {
        $fingerPrintTtl = $this->configuration->getFingerprintTtl();
        $ttl = strtotime(sprintf('-%s minutes', $fingerPrintTtl));
        $quoteCollection = $cart->getCollection();
        $quoteCollection->addFieldToFilter('fingerprint', $cart->getFingerprint());
        $quoteCollection->addFieldToFilter('is_active', 1);
        $quoteCollection->addFieldToFilter('created_at', ['gteq' => date('Y-m-d H:i:s', $ttl)]);
        $quoteCollection->setPageSize(1);

        if ($quoteCollection->getSize() > 0) {
            return false;
        }

        return true;
    }

    protected function getRemoteAddress(): string
    {
        return (string) $this->remoteAddress->getRemoteAddress();
    }

    protected function getReferer(): string
    {
        return (string) $this->request->getServer('HTTP_REFERER');
    }

    protected function getUserAgent(): string
    {
        return (string) $this->request->getServer('HTTP_USER_AGENT');
    }
}
