<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Plugin\Webapi\Controller\Soap;

class ValidateRequest
{
    protected \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configurationHelper;
    protected \MageSuite\RestApiRateLimitTerminator\Service\ValidateService $validateService;

    public function __construct(
        \MageSuite\RestApiRateLimitTerminator\Helper\Configuration $configurationHelper,
        \MageSuite\RestApiRateLimitTerminator\Service\ValidateService $validateService
    ) {
        $this->configurationHelper = $configurationHelper;
        $this->validateService = $validateService;
    }

    public function beforeDispatch(
        \Magento\Webapi\Controller\Soap $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $blockedServices = $this->configurationHelper->getSoapBlockedServicesList();
        $service = $request->getParam('services');

        foreach ($blockedServices as $blockedService) {
            if ($blockedService['service_url'] !== $service) {
                continue;
            }
            if (!$this->validateService->validate($blockedService, $service, $request)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('This service is blocked.')
                );
            }
        }

        return [$request];
    }
}
