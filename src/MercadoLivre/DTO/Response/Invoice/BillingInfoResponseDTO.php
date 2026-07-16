<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ da resposta de GET /orders/{id}/billing_info (InvoiceMethods::getBillingInfo).
 * A API devolve o wrapper `{billing_info: {...}}`.
 *
 * `toArray()` e' lossless — serve pra gravar o raw (INSERT-first).
 */
final class BillingInfoResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?BillingInfo $billingInfo = null,
    ) {}
}
