<?php

declare(strict_types=1);

namespace MageSuite\RestApiRateLimitTerminator\Model\Quote\Model\QuoteRepository;

abstract class AbstractPlugin
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
}
