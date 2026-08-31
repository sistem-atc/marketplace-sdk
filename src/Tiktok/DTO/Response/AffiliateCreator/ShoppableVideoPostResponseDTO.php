<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202607/videos.
 *
 * `quota` e' TEXTO livre ("3/day"), nao numero: e' o teto de publicacoes do
 * creator. Vem no sucesso e tambem embutido na mensagem de erro; some quando
 * nao ha limite aplicavel.
 */
final class ShoppableVideoPostResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ShoppableVideoPost $video = null,
        public readonly ?string $quota = null,
    ) {}
}
