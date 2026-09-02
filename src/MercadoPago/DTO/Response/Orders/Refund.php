<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a refund within a MercadoPago order transaction. Refunds can be total or partial,
 * and are linked to the original payment transaction. Each refund tracks its own amount, status,
 * and the items being returned.
 */
final class Refund implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Unique identifier of the refund assigned by MercadoPago. */
        public readonly ?string $id = null,

        /** Identifier of the original payment transaction being refunded. */
        public readonly ?string $transactionId = null,

        /** Seller-defined reference to correlate this refund with an external system. */
        public readonly ?string $referenceId = null,

        /** Refund amount in the order's currency (partial or full). */
        public readonly ?string $amount = null,

        /** Current refund status (e.g., "approved", "pending"). */
        public readonly ?string $status = null,

        /** End-to-end transaction identifier for Pix refunds. */
        public readonly ?string $e2eId = null,

        /** Items being refunded. Each element maps to Items. @var list<Items>|null */
        #[ArrayOf(Items::class)]
        public readonly ?array $items = null,
    ) {}
}
