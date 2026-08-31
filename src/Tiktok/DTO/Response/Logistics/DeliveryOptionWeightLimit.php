<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Limite de peso da opção de entrega. `unit` = GRAM ou POUND — comparar o peso
 * do produto sem converter a unidade é o erro clássico aqui.
 */
final class DeliveryOptionWeightLimit implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $maxWeight = null,
        public readonly ?int $minWeight = null,
        public readonly ?string $unit = null,
    ) {}
}
