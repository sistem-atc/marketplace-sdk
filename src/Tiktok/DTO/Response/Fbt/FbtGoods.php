<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * "Goods" do FBT — item de `data.goods[]` de `/fbt/202607/goods/search`.
 *
 * GOODS NAO E' PRODUTO NEM SKU. E' a entidade FISICA que o armazem do TikTok
 * movimenta, com id proprio (`id`, do sistema FBT). Um goods se liga a N SKUs
 * do TikTok Shop (`skus[]`), e so' o SKU tem produto/anuncio. Todo o resto do
 * grupo FBT (inventario, inbound, MCF) fala em `goods_id`, NUNCA em sku_id —
 * confundir os dois devolve resultado vazio sem erro.
 *
 * `referenceCode` e' o codigo definido pelo SELLER: e' por ele que o de/para
 * com o SKU do ERP deve ser feito.
 *
 * @property list<FbtGoodsBarcode>|null $barcodes
 * @property list<FbtGoodsSku>|null $skus
 */
final class FbtGoods implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        #[ArrayOf(FbtGoodsBarcode::class)]
        public readonly ?array $barcodes = null,
        public readonly ?string $referenceCode = null,
        public readonly ?string $imageUrl = null,
        public readonly ?FbtGoodsMeasurement $merchantDeclarationInfo = null,
        public readonly ?FbtGoodsMeasurement $warehouseConfirmationInfo = null,
        #[ArrayOf(FbtGoodsSku::class)]
        public readonly ?array $skus = null,
        public readonly ?FbtLotExpirationInfo $lotExpirationInfo = null,
        public readonly ?bool $isHazmat = null,
        public readonly ?FbtHazmatInfo $hazmatInfo = null,
        // SIOC_APPROVAL_STATUS_APPROVE | _REJECT | _UNDER_REVIEW
        public readonly ?string $siocApprovalStatus = null,
        // APPROVAL_STATUS_UNDER_REVIEW | _FULFILLABLE | _UNFULFILLABLE.
        // UNFULFILLABLE = o armazem nao despacha o item; e' venda travada, nao
        // so' pendencia documental.
        public readonly ?string $hazmatApprovalStatus = null,
        public readonly ?FbtBulkyItemAttribute $bulkyItemAttribute = null,
        public readonly ?FbtGoodsSafetyAttributes $goodsSafetyAttributes = null,
        public readonly ?bool $isSioc = null,
        public readonly ?FbtSiocAttribute $siocAttribute = null,
    ) {}
}
