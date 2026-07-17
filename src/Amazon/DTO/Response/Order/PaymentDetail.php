<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/** Detalhe de pagamento (`PaymentExecutionDetail[]`). */
final class PaymentDetail implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?Money $payment = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $acquirerId = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $cardBrand = null,
    ) {}
}
