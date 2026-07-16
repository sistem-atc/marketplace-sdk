<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pagamento do item (`items[].payments[]`).
 *
 * @property array<int|string, mixed> $chargesDetails
 * @property array<string, mixed>|null $transactionPayment
 * @property array<string, mixed>|null $pointOfInteraction
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $chargesDetails
     * @param  array<string, mixed>|null  $transactionPayment
     * @param  array<string, mixed>|null  $pointOfInteraction
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $paymentMethodId = null,
        public readonly ?int $installments = null,
        public readonly ?string $paymentType = null,
        public readonly ?float $transactionAmount = null,
        public readonly ?float $totalPaidAmount = null,
        public readonly ?float $sellerDiscountAmount = null,
        public readonly ?float $meliDiscountAmount = null,
        public readonly array $chargesDetails = [],
        public readonly ?string $transactionId = null,
        public readonly ?array $transactionPayment = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?array $pointOfInteraction = null,
    ) {}
}
