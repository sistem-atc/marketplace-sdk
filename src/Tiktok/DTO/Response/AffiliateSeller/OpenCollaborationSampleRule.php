<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Regra de amostra gratis de um produto na colaboracao aberta.
 *
 * `isSampleTimeUnlimited=false` significa que a janela e' `startTime`..`endTime`.
 */
final class OpenCollaborationSampleRule implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        // cota total de amostras oferecida
        public readonly ?int $sampleQuota = null,
        public readonly ?bool $isSampleTimeUnlimited = null,
        // NOT_STARTED | ONGOING | NO_LEFT_COUNT | PLAN_EXCEPTION | EXPIRED
        public readonly ?string $status = null,
        // o que sobrou da cota
        public readonly ?int $availableQuantity = null,
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?SampleRuleThresholds $thresholds = null,
    ) {}
}
