<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par `{type, value}` de `billing_info.additional_info[]` — o ML manda os dados
 * do comprador como lista chave-valor (FIRST_NAME, DOC_NUMBER, ZIP_CODE, …).
 */
final class BillingInfoAdditional implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $value = null,
    ) {}
}
