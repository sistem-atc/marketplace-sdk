<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Status de adesao do seller ao Multi Channel Fulfillment.
 *
 * `isMcf` e' INT (0/1), nao bool — a doc declara `int` e o exemplo manda `0`.
 * Tipar bool serializaria de volta como `false` e quebraria o roundtrip
 * lossless; converta no consumidor (`$s->isMcf === 1`).
 */
final class FbtMcfStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $isMcf = null,
    ) {}
}
