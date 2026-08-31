<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pacote na LISTAGEM (`packages[]` do search).
 *
 * Bem mais pobre que o PackageDetailResponseDTO: sem endereco, peso,
 * dimensao, seguro nem sub-status. Note tambem que aqui o id vem em `id` e
 * o status em `status` — no detalhe sao `package_id` e `package_status`.
 *
 * @property list<PackageOrder>|null $orders
 * @property list<string>|null $orderLineItemIds
 */
final class PackageSummary implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        #[ArrayOf(PackageOrder::class)]
        public readonly ?array $orders = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        /** PROCESSING | FULFILLING | COMPLETED | CANCELLED */
        public readonly ?string $status = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?string $shippingProviderName = null,
        public readonly ?string $shippingProviderId = null,
        /** @var list<string>|null */
        public readonly ?array $orderLineItemIds = null,
    ) {}
}
