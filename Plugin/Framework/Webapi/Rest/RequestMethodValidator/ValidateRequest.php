<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Plugin\Framework\Webapi\Rest\RequestMethodValidator;

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

    public function beforeValidate(
        \Magento\Framework\Webapi\Rest\RequestValidatorInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ): array {
        $blockedServices = $this->configurationHelper->getRestBlockedServicesList();
        $service = rtrim($request->getRequestUri(), '/');

        foreach ($blockedServices as $blockedService) {
            $blockedServiceData['service_url'] = rtrim($blockedService['service_url'], '/') ?? '';

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
