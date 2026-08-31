<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Servico de frete escolhido na criacao do pacote.
 *
 * `price` e' STRING e `currency` vem como texto livre da API ("dollar", nao
 * "USD"): nao trate como codigo ISO.
 * Os campos de prazo e de provider sao marcados "US only" na doc — no BR
 * chegam nulos.
 */
final class ShippingServiceInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $price = null,
        public readonly ?string $currency = null,
        public readonly ?int $earliestDeliveryDays = null,
        public readonly ?int $latestDeliveryDays = null,
        public readonly ?string $shippingProviderId = null,
        public readonly ?string $shippingProviderName = null,
    ) {}
}
