<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `company` — dados da empresa do seller (razão social, IE, CNPJ). */
final class UserCompany implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $brandName = null,
        public readonly ?string $corporateName = null,
        public readonly ?string $identification = null,
        public readonly ?string $cityTaxId = null,
        public readonly ?string $stateTaxId = null,
        public readonly mixed $custTypeId = null,
        public readonly ?string $softDescriptor = null,
    ) {}
}
