<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Motivo de reprovacao do pre-check. `code` aqui e' INT (o mesmo codigo de erro
 * do Create Product), diferente do `code` STRING dos diagnosticos.
 */
final class ListingCheckFailReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
    ) {}
}
