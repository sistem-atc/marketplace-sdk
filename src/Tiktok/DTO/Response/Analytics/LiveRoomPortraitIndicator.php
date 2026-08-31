<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bucket de perfil de audiencia da live: `type` e' a categoria (genero, faixa
 * etaria, seguidor/nao-seguidor ou pais) e `value` a medida — SEMPRE string.
 *
 * ATENCAO a unidade: em `regionIndicators`, `value` NAO e' contagem, e' o share
 * do pais MULTIPLICADO POR 10.000 (por-mirico). Nos demais e' contagem.
 */
final class LiveRoomPortraitIndicator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $value = null,
    ) {}
}
