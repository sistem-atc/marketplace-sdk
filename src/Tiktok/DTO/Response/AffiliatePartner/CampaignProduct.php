<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Produto inscrito numa campanha de affiliate partner.
 *
 * Três comissões convivem e NÃO são intercambiáveis, todas em centésimos de %
 * (6600 = 66,00%): `total` é o que o vendedor banca, e ela se REPARTE entre
 * `creator` (fica com o criador) e `partner` (fica com o parceiro) —
 * 1000 + 5600 = 6600. `open_collaboration` é outra coisa: a taxa da
 * colaboração aberta, fora dessa soma.
 */
final class CampaignProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** PENDING | APPROVED | REJECTED | PENDING_CLOSED | CLOSED */
        public readonly ?string $reviewStatus = null,
        public readonly ?string $name = null,
        public readonly ?string $mainImageUrl = null,
        /** Menor preço ORIGINAL entre os SKUs. */
        public readonly ?Money $lowestPrice = null,
        public readonly ?Money $highestPrice = null,
        /** Soma do estoque de todos os SKUs. */
        public readonly ?int $inventory = null,
        public readonly ?string $shopName = null,
        /** Centésimos de %. total = creator + partner. */
        public readonly ?int $totalCommissionRate = null,
        public readonly ?int $creatorCommissionRate = null,
        public readonly ?int $partnerCommissionRate = null,
        public readonly ?int $openCollaborationCommissionRate = null,
        /** false = o produto não tem URL pública; link de promoção não sai. */
        public readonly ?bool $isAvailable = null,
        public readonly ?int $productSales = null,
        public readonly ?ProductCategory $category = null,
        /** Amostras que o vendedor liberou pros criadores. */
        public readonly ?int $sampleQuota = null,
        #[ArrayOf(CampaignProductSku::class)]
        public readonly ?array $skuInformationList = null,
        /** HTML, não texto puro. */
        public readonly ?string $productDescription = null,
    ) {}
}
