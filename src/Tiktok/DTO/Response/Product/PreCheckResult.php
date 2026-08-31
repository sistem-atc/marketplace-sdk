<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Verificacao de informacao do produto (ex.: INCOMPLETE_INFO). */
final class PreCheckResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $preCheckItem = null,
        #[ArrayOf(PreCheckDetail::class)]
        public readonly ?array $preCheckDetails = null,
    ) {}
}
