<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 15 — Product information change.
 *
 * So' dispara pra produto LIVE, e apenas quando a alteracao ja' esta' VIGENTE
 * online — nao serve pra acompanhar edicao em rascunho/em auditoria.
 *
 * `changedFields` diz O QUE mudou, nunca o valor novo: pra ler o conteudo e'
 * preciso chamar Get Product.
 */
final class ProductInformationChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $changedFields */
    public function __construct(
        public readonly ?string $productId = null,
        // SELLER_CENTER | OPEN_API — de onde partiu a edicao. Util pra ignorar
        // o eco da nossa propria escrita via API.
        public readonly ?string $changeSource = null,
        // title | description | main_images | product_attributes |
        // size_chart_image | size_chart_template | others
        public readonly ?array $changedFields = null,
        public readonly ?int $updateTime = null,
    ) {}
}
