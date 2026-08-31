<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa de exportacao offline do Compass (analytics de afiliados).
 */
final class CompassTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // CREATOR | BASE
        public readonly ?string $moduleType = null,
        public readonly ?string $id = null,
        public readonly ?string $fileName = null,
        // RUNNING | SUCCEEDED | FAILED
        public readonly ?string $status = null,
    ) {}
}
