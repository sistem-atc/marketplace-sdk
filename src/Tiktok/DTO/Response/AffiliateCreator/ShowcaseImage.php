<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem de produto da vitrine.
 *
 * ⚠️ A chave da altura e `heigth` — typo do TikTok na API, nao no SDK.
 * Corrigir pra `height` faria o campo ser DESCARTADO na hidratacao.
 */
final class ShowcaseImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $width = null,
        public readonly ?int $heigth = null,
        public readonly ?string $url = null,
    ) {}
}
