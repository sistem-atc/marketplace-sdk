<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de `POST /fbt/202603/goods/hazmat_and_expiration_info/search`. */
final class FbtGoodsHazmatExpirationResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtHazmatAndExpirationInfo $hazmatAndExpirationInfo = null,
    ) {}
}
