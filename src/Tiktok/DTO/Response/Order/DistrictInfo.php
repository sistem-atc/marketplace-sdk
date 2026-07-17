<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Nível do endereço (`recipient_address.district_info[]`).
 *
 * O TikTok não devolve cidade/estado em campos próprios: manda uma LISTA de
 * níveis (`addressLevel` = L0/L1/L2..., `addressLevelName` = "Country"/
 * "State"/"City"), e cada um traz o `addressName`. Pra extrair a cidade,
 * procure o nível pelo nome — não confie na posição.
 */
final class DistrictInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $addressLevel = null,
        public readonly ?string $addressLevelName = null,
        public readonly ?string $addressName = null,
        public readonly ?string $isoCode = null,
    ) {}
}
