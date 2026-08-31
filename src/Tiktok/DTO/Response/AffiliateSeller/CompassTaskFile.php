<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Arquivo da exportacao — o conteudo INTEIRO vem em base64 no JSON, nao ha
 * URL de download. Planilha grande vira resposta grande; decodifique e grave
 * em disco em vez de segurar em memoria.
 */
final class CompassTaskFile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $base64 = null,
    ) {}
}
