<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * `items[].AmazonPrograms`. `Programs` é lista de shape livre (passthrough).
 *
 * @property array<int, mixed>|null $programs
 */
final class AmazonPrograms implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?array $programs = null,
    ) {}
}
