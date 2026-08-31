<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Cota semanal de abordagem a creators.
 *
 * QUASE TUDO E' STRING, inclusive contadores e timestamps — `quotaSum`,
 * `quotaUse` e `start/endTime` vem entre aspas. E os tempos estao em
 * MILISSEGUNDOS (1754296736852), nao em segundos.
 *
 * Cota gasta = `quotaUse`/`quotaSum`, a menos que `unlimited=true`.
 */
final class CreatorOutreachQuota implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $shopId = null,
        // BONUS | period quota
        public readonly ?string $quotaType = null,
        public readonly ?string $gmvTier = null,
        // contador, mas a API manda string
        public readonly ?string $quotaSum = null,
        public readonly ?string $quotaUse = null,
        public readonly ?bool $unlimited = null,
        public readonly ?string $reachedMaxFollowerCnt = null,
        public readonly ?string $newConnectCount = null,
        public readonly ?string $pairConnectCount = null,
        // epoch em MILISSEGUNDOS, como string
        public readonly ?string $startTime = null,
        // epoch em MILISSEGUNDOS, como string
        public readonly ?string $endTime = null,
        #[ArrayOf(CreatorOutreachGmvLevelRule::class)]
        public readonly ?array $gmvLevelRule = null,
    ) {}
}
