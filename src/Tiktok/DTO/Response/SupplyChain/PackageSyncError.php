<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\SupplyChain;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Falha por PACOTE dentro de um lote aceito pela API. */
final class PackageSyncError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** STRING aqui ("39003015"), ao contrário do `code` int do envelope. */
        public readonly ?string $code = null,
        public readonly ?string $message = null,
        public readonly ?PackageSyncErrorDetail $detail = null,
    ) {}
}
