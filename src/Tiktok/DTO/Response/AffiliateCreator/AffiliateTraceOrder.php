<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido rastreado por sharing link — item de `orders[]`.
 *
 * ⚠️ A doc declara `id` como INT e o exemplo oficial manda numero cru, mas e'
 * um snowflake de 18 digitos: como int em JS/PHP-32 perde precisao. O DTO
 * forca STRING (o AutoHydrate converte) — compare sempre como texto.
 *
 * `status`: UNSPECIFIED | ORDERED | SETTLED | REFUNDED | FROZEN | DEDUCTED.
 *
 * @property list<AffiliateTraceOrderSku>|null $skus
 */
final class AffiliateTraceOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $status = null,
        #[ArrayOf(AffiliateTraceOrderSku::class)]
        public readonly ?array $skus = null,
        public readonly ?int $createTime = null,
        public readonly ?int $deliveryTime = null,
    ) {}
}
