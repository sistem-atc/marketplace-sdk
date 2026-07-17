<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido Magalu — resposta de `/seller/v1/orders/{id}`.
 *
 * PARTICULARIDADES:
 * - DINHEIRO é INT NORMALIZADO (objeto {total/value, normalizer}), não decimal.
 *   Ver Money. Nunca leia o inteiro como reais — use `->amount()`.
 * - As linhas/itens moram em `deliveries[].items[]`, não num `items[]` de topo:
 *   1 pedido pode ter N entregas (split por seller/logística).
 * - A NFe (chave SEFAZ) está em `deliveries[].invoices[].key`.
 * - `provider.extras.isFulfillment` marca Magalu Entregas (2 NFes).
 * - Magalu e Netshoes vêm pela MESMA API; `channel.extras.alias` separa.
 * - Datas são ISO-8601 (string). PII (customer/recipient) sem máscara.
 *
 * @property list<Delivery>|null $deliveries
 * @property list<Payment>|null $payments
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $code = null,
        public readonly ?string $status = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $approvedAt = null,
        public readonly ?string $purchasedAt = null,
        public readonly ?Amounts $amounts = null,
        public readonly ?Channel $channel = null,
        public readonly ?SourceChannel $sourceChannel = null,
        public readonly ?Customer $customer = null,
        #[ArrayOf(Payment::class)]
        public readonly ?array $payments = null,
        #[ArrayOf(Delivery::class)]
        public readonly ?array $deliveries = null,
    ) {}
}
