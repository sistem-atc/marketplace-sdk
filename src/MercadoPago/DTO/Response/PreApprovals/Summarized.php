<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * PreApproval Summarized billing resource. Provides a summary of the subscription's billing
 * history, including how many installments have been charged, how many are pending, total amounts
 * charged and pending, and the date/amount of the last successful charge.
 */
final class Summarized implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** The number of quotas (or installments). */
        public readonly ?int $quotas = null,

        /** The quantity that has been charged. */
        public readonly ?int $chargedQuantity = null,

        /** The quantity that is pending charge. */
        public readonly ?int $pendingChargeQuantity = null,

        /** The amount that has been charged. */
        public readonly ?float $chargedAmount = null,

        /** The amount that is pending charge. */
        public readonly ?float $pendingChargeAmount = null,

        /** The date of the last charge. */
        public readonly ?string $lastChargedDate = null,

        /** The amount of the last charge. */
        public readonly ?float $lastChargedAmount = null,

        /** The semaphore status. */
        public readonly mixed $semaphore = null,
    ) {}
}
