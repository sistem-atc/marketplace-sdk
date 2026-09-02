<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Method Settings resource. Groups the card validation settings for a payment method,
 * including BIN patterns, card number length/validation rules, and security code requirements.
 * Fields are mapped to nested DTOs: - bin -> Bin - card_number -> CardNumber - security_code ->
 * SecurityCode
 */
final class Settings implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Setting bin. */
        public readonly ?Bin $bin = null,

        /** Setting card number. */
        public readonly ?CardNumber $cardNumber = null,

        /** Setting security code. */
        public readonly ?SecurityCode $securityCode = null,
    ) {}
}
