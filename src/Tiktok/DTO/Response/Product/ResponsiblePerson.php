<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pessoa responsavel cadastrada (compliance GPSR da UE) — o contato exigido
 * dentro da UE pra produtos importados.
 *
 * Cadastro de LOJA: o produto so' referencia o id em `responsiblePersonIds`.
 *
 * @property list<ResponsiblePersonRegionalProfile>|null $regionalProfiles
 */
final class ResponsiblePerson implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        #[ArrayOf(ResponsiblePersonRegionalProfile::class)]
        public readonly ?array $regionalProfiles = null,
    ) {}
}
