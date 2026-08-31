<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Comissao do produto dentro do convite dirigido.
 *
 * `effectiveTime` vem como STRING aqui ("1715654330") embora seja epoch —
 * quirk desta rota; tipar int quebraria o roundtrip.
 * `minimumAmount`/`maximumAmount` sao a comissao ESTIMADA em dinheiro pros
 * SKUs do produto.
 */
final class TargetCollaborationProductCommission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // centesimos de %: 3000 = 30,00%; faixa [100, 8000]
        public readonly ?int $rate = null,
        // epoch em segundos, mas a API manda como string
        public readonly ?string $effectiveTime = null,
        public readonly ?string $currency = null,
        public readonly ?string $minimumAmount = null,
        public readonly ?string $maximumAmount = null,
        // taxa so pros pedidos vindos de ads
        public readonly ?int $shopAdsCommissionRate = null,
    ) {}
}
