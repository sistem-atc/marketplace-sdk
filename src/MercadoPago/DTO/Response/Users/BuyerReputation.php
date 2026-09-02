<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Buyer Reputation resource. Represents the buying reputation of a MercadoLibre user,
 * including the count of cancelled transactions, reputation tags, and detailed transaction
 * statistics.
 */
final class BuyerReputation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of canceled transactions. */
        public readonly ?int $canceledTransactions = null,

        /** User tags associated with the buyer reputation. */
        public readonly ?array $tags = null,

        /** User transaction metrics and statistics. */
        public readonly ?BuyerReputationTransactions $transactions = null,
    ) {}
}
