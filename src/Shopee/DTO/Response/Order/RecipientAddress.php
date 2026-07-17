<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereço de entrega (`recipient_address`).
 *
 * ATENÇÃO: a Shopee MASCARA a PII (nome/telefone/endereço vêm `****`) até o
 * app ter o escopo de unmask liberado no Console — ver alerta
 * shopee-pii-mascarada-escopo-unmask. Campo preenchido com `*` é dado
 * mascarado pela Shopee, não bug de parse.
 */
final class RecipientAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $phone = null,
        public readonly ?string $town = null,
        public readonly ?string $district = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $region = null,
        public readonly ?string $zipcode = null,
        public readonly ?string $fullAddress = null,
    ) {}
}
