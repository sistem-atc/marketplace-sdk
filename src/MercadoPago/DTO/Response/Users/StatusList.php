<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Users;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * User Status List resource. Represents the permission status for a specific user action (buy,
 * sell, or list), including whether the action is allowed, any restriction codes, and immediate
 * payment requirements.
 */
final class StatusList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Indicates whether buying is allowed (true/false). */
        public readonly ?bool $allow = null,

        /** Buy status codes. */
        public readonly ?array $codes = null,

        /** User immediate payment data for buying. */
        public readonly ?StatusBuyImmediatePayment $immediatePayment = null,
    ) {}
}
