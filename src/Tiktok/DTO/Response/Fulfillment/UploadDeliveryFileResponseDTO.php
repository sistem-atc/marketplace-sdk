<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado do upload de PDF de comprovacao de entrega.
 *
 * A `url` retornada e' o que se passa em `file_url` no
 * updatePackageDeliveryStatus — o arquivo em si nao vai no update.
 */
final class UploadDeliveryFileResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $url = null,
        public readonly ?string $name = null,
    ) {}
}
