<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /product/202606/skpps/{product_id}`.
 *
 * `updateTime` NAO e' "agora": e' a data do snapshot offline do score, sempre
 * fixada em 23:59:59 UTC do dia, com atraso de T+1 (as vezes T+2). Nao use pra
 * decidir se o dado esta' fresco em granularidade de hora.
 */
final class ProductSkppDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $affiliateProgramStatus = null,
        public readonly ?int $totalScore = null,
        public readonly ?int $targetScore = null,
        #[ArrayOf(SkppReward::class)]
        public readonly ?array $rewards = null,
        #[ArrayOf(SkppTaskDetail::class)]
        public readonly ?array $taskDetails = null,
        public readonly ?int $updateTime = null,
        public readonly ?string $skppStatus = null,
    ) {}
}
