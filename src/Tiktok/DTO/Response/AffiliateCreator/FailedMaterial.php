<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Material pro qual o link NAO pode ser gerado (produto esgotado etc.).
 * Falha parcial: o batch devolve 200 com sucessos e falhas lado a lado.
 */
final class FailedMaterial implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $materialId = null,
        public readonly ?string $failReason = null,
    ) {}
}
