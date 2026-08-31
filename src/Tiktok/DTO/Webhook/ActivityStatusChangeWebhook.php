<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 39 — Activity status change (promocao).
 *
 * So' dispara com o escopo `Promotion Information` autorizado, so' pros tipos
 * FIXED_PRICE, DIRECT_DISCOUNT, SHIPPING_DISCOUNT, BUY_MORE_SAVE_MORE e
 * FLASHSALE (esse ultimo so' existe no SEA: SG, TH, ID, MY, PH, VN), e so' nas
 * transicoes NOT_START->ONGOING, ONGOING->EXPIRED e ->DEACTIVATED.
 *
 * ⚠️ O payload traz o status NOVO (`status`), nunca o anterior. Quem quiser
 * saber "de onde veio" precisa ter guardado o estado.
 *
 * Nao confundir com o topico 63 (Activity change), que fala de MUDANCA DE
 * CONTEUDO da campanha (produtos incluidos/removidos) e nao de status.
 */
final class ActivityStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $activityId = null,
        /** Epoch em SEGUNDOS da mudanca de status. */
        public readonly ?int $updateTime = null,
        /** FIXED_PRICE | DIRECT_DISCOUNT | SHIPPING_DISCOUNT | BUY_MORE_SAVE_MORE | FLASHSALE */
        public readonly ?string $activityType = null,
        /** PRODUCT | VARIATION | SHOP */
        public readonly ?string $productLevel = null,
        /** Status NOVO: ONGOING | EXPIRED | DEACTIVATED */
        public readonly ?string $status = null,
    ) {}
}
