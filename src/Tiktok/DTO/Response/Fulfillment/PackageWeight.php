<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Peso do pacote. `value` e' STRING ("1.2") — tipar float comeria a casa
 * decimal e quebraria o roundtrip lossless.
 */
final class PackageWeight implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $value = null,
        /** GRAM | POUND */
        public readonly ?string $unit = null,
    ) {}
}
