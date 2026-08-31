<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `POST /affiliate_seller/202603/compass/offline_task`.
 *
 * O create so' ENFILEIRA: pegue o id, acompanhe em `getCompassTasks` ate
 * `status=SUCCEEDED` e so' entao baixe o arquivo.
 */
final class CompassTaskCreateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?CompassTaskRef $task = null,
    ) {}
}
