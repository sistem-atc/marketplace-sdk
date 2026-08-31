<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Template de frete do seller. `templateParty`: 1 = frete pela plataforma,
 * 2 = frete pelo seller. `templateId` é STRING (snowflake).
 */
final class ShippingTemplate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $templateName = null,
        public readonly ?string $templateId = null,
        public readonly ?bool $isDefault = null,
        public readonly ?int $templateParty = null,
    ) {}
}
