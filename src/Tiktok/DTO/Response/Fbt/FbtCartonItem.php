<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item dentro de uma caixa de inbound.
 *
 * `expirationTimestamp` e' epoch em segundos mas chega como STRING (a doc
 * declara string, ao contrario de `expiration_time` do recebimento, que e'
 * int). Tipar int aqui quebraria o roundtrip.
 */
final class FbtCartonItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $goodsId = null,
        public readonly ?int $quantity = null,
        public readonly ?string $lotCode = null,
        public readonly ?string $expirationTimestamp = null,
    ) {}
}
