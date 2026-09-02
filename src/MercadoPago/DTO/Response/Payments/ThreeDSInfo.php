<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Payments;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents 3D Secure (3DS) authentication data for a card payment in the MercadoPago API.
 * Contains the challenge URL and request payload needed to complete 3DS cardholder verification
 * when required by the issuer. Nested within Payment.
 */
final class ThreeDSInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** URL to the 3DS challenge page where the cardholder completes authentication. */
        public readonly ?string $externalResourceUrl = null,

        /** Base64-encoded Challenge Request (CReq) message for the 3DS flow. */
        public readonly ?string $creq = null,
    ) {}
}
