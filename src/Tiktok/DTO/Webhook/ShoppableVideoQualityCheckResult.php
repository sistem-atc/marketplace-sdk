<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.good_quality_check_result` do topico 59 — avaliacao de QUALIDADE
 * (resolucao, etc.), independente da checagem de violacao. Um pode passar e o
 * outro falhar no mesmo evento.
 */
final class ShoppableVideoQualityCheckResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ShoppableVideoQualityIssue>|null $issues */
    public function __construct(
        /** SUCCESS | FAIL | PROCESSING */
        public readonly ?string $status = null,
        #[ArrayOf(ShoppableVideoQualityIssue::class)]
        public readonly ?array $issues = null,
    ) {}
}
