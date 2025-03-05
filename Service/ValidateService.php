<?php

namespace MageSuite\RestApiRateLimitTerminator\Service;

class ValidateService
{
    protected \Magento\Framework\AuthorizationInterface $authorization;

    public function __construct(\Magento\Framework\AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    public function validate(
        array $blockedService,
        string $endpoint,
        \Magento\Framework\App\RequestInterface $request
    ): bool {
        return !$this->matchServiceAndMethod($blockedService, $endpoint, $request) || $this->isAuthorized($blockedService);
    }

    protected function isAuthorized($blockedService): bool
    {
        return $this->authorization->isAllowed($blockedService['service_acl']);
    }

    protected function matchServiceAndMethod(
        array $blockedServiceData,
        string $endpoint,
        \Magento\Framework\App\RequestInterface $request
    ): bool {
        $blockedServiceMethod = $blockedServiceData['http_method'] ?? '';
        $endpoint = rtrim($endpoint, '/');

        $matchEndpoint = $endpoint === $blockedServiceData['service_url'];
        $matchHttpMethod = !empty($blockedServiceData['http_method']) ? $request->getMethod() === $blockedServiceMethod : true;

        return  $matchEndpoint && $matchHttpMethod;
    }
}
