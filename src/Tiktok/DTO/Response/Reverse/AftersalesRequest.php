<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Aftersales request — o guarda-chuva do pos-venda: o que o COMPRADOR pediu.
 *
 * Hierarquia que o TikTok usa (e que confunde quem chega do ML/Shopee):
 *   aftersales request  -> o pedido do comprador (1 por solicitacao)
 *     sku return request -> a devolucao POR SKU (o dinheiro esta' aqui)
 *       RMA              -> o pacote fisico que volta (pode nao existir)
 *
 * @property list<AftersalesLineItem>|null $lineItems
 * @property list<SkuReturnRequest>|null $skuReturnRequests
 * @property list<ReturnMerchandiseAuthorization>|null $returnMerchandiseAuthorizations
 */
final class AftersalesRequest implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /**
         * So' vem com `LINE_ITEMS` no `whitelisted_data_fields`. Idem
         * `skuReturnRequests` (SKU_RETURN_REQUESTS) e
         * `returnMerchandiseAuthorizations` (RETURN_MERCHANDISE_AUTHORIZATIONS).
         * Sem o whitelist a busca devolve praticamente so' id + status.
         */
        #[ArrayOf(AftersalesLineItem::class)]
        public readonly ?array $lineItems = null,
        /** INIT | PENDING_REQUEST_REVIEW | REQUEST_REVIEW_COMPLETED */
        public readonly ?string $status = null,
        #[ArrayOf(SkuReturnRequest::class)]
        public readonly ?array $skuReturnRequests = null,
        #[ArrayOf(ReturnMerchandiseAuthorization::class)]
        public readonly ?array $returnMerchandiseAuthorizations = null,
    ) {}
}
