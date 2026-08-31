<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 19 — Size chart change.
 *
 * O evento e' do TEMPLATE de tabela de medidas, nao de um produto: nao vem
 * `product_id`. Um template editado afeta todos os anuncios associados a ele.
 * Envelope traz `seller_open_id` no lugar de `shop_id`.
 */
final class SizeChartChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sizeChartTemplateId = null,
        public readonly ?string $sizeChartTemplateName = null,
        // CREATE | EDIT | DELETE
        public readonly ?string $eventType = null,
        public readonly ?int $updateTime = null,
    ) {}
}
