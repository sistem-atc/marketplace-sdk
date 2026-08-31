<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem principal do produto no ranking de bestsellers.
 *
 * `urls`/`thumbUrls` sao declarados []string na doc, mas o exemplo OFICIAL manda
 * string crua em `urls`. Tipar `array` DESCARTARIA o valor quando vier escalar,
 * entao ficam `mixed`: preserva os dois formatos sem perda.
 */
final class BestsellingProductImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $height = null,
        public readonly ?int $width = null,
        public mixed $thumbUrls = null,
        public readonly ?string $uri = null,
        public mixed $urls = null,
    ) {}
}
