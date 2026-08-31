<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Unidades perdidas do produto, em quatro estados que NAO se sobrepoem:
 * `canceled` (pedido nao pago ou aguardando envio), `refunded` (estornado sem
 * devolucao fisica), `returned` (devolvido e estornado) e `replacements`
 * (devolvido e trocado).
 */
final class ShopProductCancelAndRefunds implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $returned = null,
        public readonly ?int $canceled = null,
        public readonly ?int $refunded = null,
        public readonly ?int $replacements = null,
    ) {}
}
