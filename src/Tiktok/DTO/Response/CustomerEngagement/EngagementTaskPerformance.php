<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Desempenho de uma task de engajamento. */
final class EngagementTaskPerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** SENDING | FAILED | CANCELED | SUCCESS */
        public readonly ?string $status = null,
        public readonly ?int $sentRecipientCount = null,
        public readonly ?int $readRecipientCount = null,
        public readonly ?int $orderCount = null,
        /** Dinheiro é STRING ("5.5"). Sem moeda declarada — é a da loja. */
        public readonly ?string $gmvAmount = null,
        public readonly ?int $claimedCouponsCount = null,
    ) {}
}
