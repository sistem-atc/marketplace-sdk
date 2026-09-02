<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Point;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Payment Intent List resource (Point integration). Represents the list of payment intent events
 * for a Point device. Each event contains the intent ID, status, and creation timestamp.
 */
final class PaymentIntentListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Events. @var list<Events>|null */
        #[ArrayOf(Events::class)]
        public readonly ?array $events = null,
    ) {}
}
