<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Par chave/valor da distribuicao de seguidores (localizacao, idade, genero).
 * O `value` e' FRACAO em string: "0.3705" = 37,05%.
 */
final class FollowerDistributionEntry implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $key = null,
        // fracao: "0.3705" = 37,05%
        public readonly ?string $value = null,
    ) {}
}
