<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Buyer Reputation Transactions resource. Provides a breakdown of a buyer's transaction
 * history for a given period, including cancelled, completed, not-yet-rated, and unrated
 * transaction counts. Fields are mapped to nested DTOs: - canceled -> BuyerReputationCancelled -
 * not_yet_rated -> BuyerReputationNotYetRated - unrated -> BuyerReputationUnrated
 */
final class BuyerReputationTransactions implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** User metrics for canceled transactions. */
        public readonly ?BuyerReputationCancelled $canceled = null,

        /** The number of completed transactions. */
        public readonly mixed $completed = null,

        /** User metrics for transactions not yet rated. */
        public readonly ?BuyerReputationNotYetRated $notYetRated = null,

        /** The transaction period (e.g., "historic"). */
        public readonly ?string $period = null,

        /** User metrics for unrated transactions. */
        public readonly ?BuyerReputationUnrated $unrated = null,

        /** Total of transactions. */
        public readonly mixed $total = null,
    ) {}
}
