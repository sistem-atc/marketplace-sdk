<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RMA — a AUTORIZACAO de retorno, ou seja, o PACOTE fisico que volta.
 *
 * Um aftersales request (o pedido do comprador) pode gerar varios RMAs, e um
 * RMA pode juntar itens de mais de uma return. Quem controla recebimento de
 * mercadoria acompanha o `status` DAQUI, nao o `returnStatus` da return.
 *
 * @property list<AftersalesLineItem>|null $lineItems
 * @property list<SkuReturnRequest>|null $skuReturnRequests
 */
final class ReturnMerchandiseAuthorization implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** Pacote fisico associado. */
        public readonly ?string $packageId = null,
        public readonly ?string $aftersalesRequestId = null,
        /** INIT | AWAITING_BUYER_RETURN | ... */
        public readonly ?string $status = null,
        #[ArrayOf(AftersalesLineItem::class)]
        public readonly ?array $lineItems = null,
        /**
         * So' vem quando `SKU_RETURN_REQUESTS` esta' no
         * `whitelisted_data_fields` da busca — por padrao a API OMITE.
         */
        #[ArrayOf(SkuReturnRequest::class)]
        public readonly ?array $skuReturnRequests = null,
    ) {}
}
