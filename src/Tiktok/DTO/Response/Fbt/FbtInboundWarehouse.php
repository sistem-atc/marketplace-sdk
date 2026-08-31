<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Armazem de destino da ordem de inbound.
 *
 * `type` distingue HUB (ponto de consolidacao) de FC (centro de distribuicao).
 * Importa pro estoque: mercadoria parada num HUB ainda NAO conta como recebida
 * no FC — os contadores de recebimento so' sobem quando ela e' transferida.
 */
final class FbtInboundWarehouse implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $fbtWarehouseId = null,
        /** @var list<string>|null ids equivalentes no TikTok Shop */
        public readonly ?array $businessWarehouseIds = null,
        public readonly ?string $name = null,
        // HUB | FC
        public readonly ?string $type = null,
    ) {}
}
