<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de afiliado. Datas em epoch de SEGUNDOS.
 *
 * `status` esta depreciado (a doc diz que nao retorna valor) — a situacao real
 * vive por SKU, em `skus[].settlementStatus`.
 */
final class AffiliateOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $deliveryTime = null,
        public readonly ?int $createTime = null,
        // DEPRECIADO — ver skus[].settlementStatus
        public readonly ?string $status = null,
        #[ArrayOf(AffiliateOrderSku::class)]
        public readonly ?array $skus = null,
    ) {}
}
