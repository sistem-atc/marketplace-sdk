<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * SKU do TikTok Shop vinculado a um goods FBT.
 *
 * `matched=false` e' o achado operacional deste endpoint: existe goods no
 * armazem mas ele NAO esta' amarrado ao SKU do anuncio — o estoque fisico nao
 * abastece a venda. E' o equivalente do de/para MLB<->produto do Full.
 */
final class FbtGoodsSku implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $imageUrl = null,
        public readonly ?FbtGoodsProduct $product = null,
        /** @var list<string>|null regioes de venda, ISO 3166-1 alpha-2 */
        public readonly ?array $regions = null,
        public readonly ?bool $matched = null,
    ) {}
}
