<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Seller Reputation resource. Represents the selling reputation of a MercadoLibre user,
 * including their seller level, power seller status, transaction history, and performance
 * metrics. Fields are mapped to nested DTOs: - transactions -> Transactions - metrics -> Metrics
 */
final class SellerReputation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The seller's level ID (null in this case). */
        public readonly ?string $levelId = null,

        /** The power seller status (null in this case). */
        public readonly ?string $powerSellerStatus = null,

        /** User transaction metrics and statistics. */
        public readonly ?Transactions $transactions = null,

        /** User transaction metrics. */
        public readonly ?Metrics $metrics = null,
    ) {}
}
