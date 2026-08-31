<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de AMOSTRA do creator — item de `sample_applications[]` (busca) e de
 * `sample_application` (detalhe).
 *
 * ⚠️ SINAL FISCAL: `mainOrderId` e' o pedido gerado DEPOIS que o vendedor
 * aprova a amostra. Ou seja: todo order_id que aparecer aqui NASCEU de amostra
 * — nao e' venda. `type` diz de que especie:
 *   - FREE_SAMPLE       -> amostra gratis do proprio vendedor
 *   - SAMPLE_COUPON     -> cupom de amostra (creator paga preco com desconto)
 *   - SAMPLE_CAMPAIGN   -> campanha da plataforma, amostra bancada pelo TikTok
 *   - PLATFORM_FREE_SAMPLE / REFUNDABLE_SAMPLE aparecem na doc de outros
 *     endpoints do mesmo dominio.
 *
 * `createTime` em epoch de SEGUNDOS.
 */
final class SampleApplication implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $createTime = null,
        public readonly ?SampleProduct $sampleProduct = null,
        // O pedido que a amostra vira quando aprovada — a chave do cruzamento
        // com o pedido do ERP.
        public readonly ?string $mainOrderId = null,
        public readonly ?string $activityId = null,
        public readonly ?string $type = null,
        public readonly ?string $status = null,
        public readonly ?CreatorFulfillment $creatorFulfillment = null,
    ) {}
}
