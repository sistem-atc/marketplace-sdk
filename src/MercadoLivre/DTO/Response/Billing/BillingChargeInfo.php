<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `charge_info` — o núcleo da cobrança: tipo, valor, status e nota legal.
 */
final class BillingChargeInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $detailId = null,
        public readonly ?float $detailAmount = null,
        public readonly ?string $detailType = null,
        public readonly ?string $detailSubType = null,
        public readonly ?string $transactionDetail = null,
        public readonly ?string $status = null,
        public readonly ?string $statusDescription = null,
        public readonly ?string $creationDateTime = null,
        public readonly ?string $legalDocumentNumber = null,
        public readonly ?string $legalDocumentStatus = null,
        public readonly ?string $legalDocumentStatusDescription = null,
        public readonly mixed $debitedFromOperation = null,
        public readonly ?string $debitedFromOperationDescription = null,
        public readonly mixed $chargeBonifiedId = null,
    ) {}
}
