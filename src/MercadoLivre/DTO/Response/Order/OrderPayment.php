<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pagamento de um pedido ML (`payments[]`). Shape distinto do Invoice/Payment —
 * este é o pagamento do pedido (valor, fee do marketplace, parcelas, status).
 *
 * @property array<int|string, mixed> $availableActions
 */
final class OrderPayment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $availableActions */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $orderId = null,
        public readonly ?int $payerId = null,
        // collector = objeto {id}; atmTransferReference = objeto {company_id,...}.
        public readonly mixed $collector = null,
        public readonly ?string $status = null,
        public readonly ?string $statusCode = null,
        public readonly ?string $statusDetail = null,
        public readonly ?string $reason = null,
        public readonly ?string $paymentType = null,
        public readonly ?string $paymentMethodId = null,
        public readonly ?string $operationType = null,
        public readonly ?string $currencyId = null,
        public readonly ?float $transactionAmount = null,
        public readonly ?float $transactionAmountRefunded = null,
        public readonly ?float $totalPaidAmount = null,
        public readonly ?float $overpaidAmount = null,
        public readonly ?float $installmentAmount = null,
        public readonly ?int $installments = null,
        public readonly ?int $deferredPeriod = null,
        public readonly ?float $marketplaceFee = null,
        public readonly ?float $shippingCost = null,
        public readonly ?float $taxesAmount = null,
        public readonly ?float $couponAmount = null,
        public readonly ?int $couponId = null,
        public readonly ?int $cardId = null,
        public readonly ?int $issuerId = null,
        public readonly ?string $siteId = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $transactionOrderId = null,
        public readonly mixed $atmTransferReference = null,
        public readonly ?string $referenceId = null,
        public readonly ?string $activationUri = null,
        public readonly ?string $dateCreated = null,
        public readonly ?string $dateApproved = null,
        public readonly ?string $dateLastModified = null,
        public readonly array $availableActions = [],
        public readonly mixed $visibleBy = null,
    ) {}
}
