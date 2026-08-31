<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto na tela de promocao do creator dentro do convite dirigido.
 *
 * DUAS DIFERENCAS DO RESTO DO GRUPO:
 * 1. `commissionEffectiveTime`/`adsCommissionEffectiveTime` estao em
 *    MILISSEGUNDOS (1786592565735), nao segundos como todo o resto.
 * 2. `productStatus` e' um CODIGO NUMERICO em string ("1"=ativo, "2"=esgotado,
 *    "3"=desativado pelo seller, "4"=pela plataforma, "5"=rejeitado,
 *    "6"=apagado) — nao o enum textual LIVE/OUT_OF_STOCK das outras rotas.
 *
 * Os `old*` guardam a comissao anterior enquanto a nova nao entra em vigor.
 */
final class CreatorPromotionProductBaseInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $title = null,
        public readonly ?AffiliateImage $image = null,
        public readonly ?CreatorPromotionPriceRange $price = null,
        public readonly ?CreatorPromotionPriceRange $promotionPrice = null,
        // centesimos de %: 2200 = 22%
        public readonly ?int $targetCommission = null,
        public readonly ?int $openCommission = null,
        public readonly ?int $targetAdsCommission = null,
        public readonly ?int $oldTargetCommission = null,
        public readonly ?int $oldTargetAdsCommission = null,
        // MILISSEGUNDOS (o resto do grupo usa segundos)
        public readonly ?int $commissionEffectiveTime = null,
        // MILISSEGUNDOS
        public readonly ?int $adsCommissionEffectiveTime = null,
        // codigo numerico em string: 1=ativo 2=esgotado 3=off pelo seller 4=off pela plataforma 5=rejeitado 6=apagado
        public readonly ?string $productStatus = null,
    ) {}
}
