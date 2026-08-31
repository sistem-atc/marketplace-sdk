<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/fulfillment/202309/orders/{order_id}/shipping_services/query`.
 *
 * ARMADILHA DE NOME: `order_line_id` e' SINGULAR na doc mas o valor e' uma
 * LISTA de ids (`["32132124331234"]`). Tipar string faria o AutoHydrate cair
 * no default e o campo sumir.
 *
 * `weight`/`dimension` sao o que a API ASSUMIU pra cotar (o que foi enviado no
 * corpo, ou o cadastro do produto quando nada foi enviado) — conferir antes de
 * confiar no preco.
 *
 * @property list<EligibleShippingService>|null $shippingServices
 */
final class EligibleShippingServiceResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /** @var list<string>|null Singular na doc, lista no payload. */
        public readonly ?array $orderLineId = null,
        public readonly ?PackageWeight $weight = null,
        #[ArrayOf(EligibleShippingService::class)]
        public readonly ?array $shippingServices = null,
        public readonly ?PackageDimension $dimension = null,
    ) {}
}
