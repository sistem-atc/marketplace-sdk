<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GET /billing/integration/periods/key/{key}/summary/details — o fechamento do
 * período: o que a fatura inclui e o que já foi cobrado.
 *
 * `billIncludes` = {total_amount, total_perception, bonuses[], charges[]}
 * `paymentCollected` = {total_payment, total_collected, total_debt, ...}
 * Ficam crus: shape volátil e o consumidor lê campo a campo.
 */
final class BillingSummaryResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int|string, mixed> $errors */
    public function __construct(
        public readonly mixed $user = null,
        public readonly mixed $period = null,
        public readonly mixed $billIncludes = null,
        public readonly mixed $paymentCollected = null,
        public readonly array $errors = [],
    ) {}
}
