<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resultado de um item de pre-requisito da loja. */
final class PrerequisiteCheckResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $checkItem = null,
        public readonly ?bool $isFailed = null,
        public readonly ?array $failReasons = null,
    ) {}
}
