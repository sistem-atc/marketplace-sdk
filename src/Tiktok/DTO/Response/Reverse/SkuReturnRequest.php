<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Devolucao no nivel de SKU ("return") — o objeto mais denso do grupo.
 *
 * E' o que fecha titulo no Contas a Receber: `refundAmount->refundTotal` e'
 * quanto volta pro comprador e `returnStatus` diz se ja' virou dinheiro.
 *
 * TRES ARMADILHAS:
 *
 * 1. `returnType = REFUND` NAO tem retorno fisico de mercadoria — o comprador
 *    fica com o produto. So' `RETURN_AND_REFUND` gera nota de entrada.
 * 2. `role` diz quem abriu (BUYER/SELLER/OPERATOR). OPERATOR e' o TikTok
 *    decidindo por arbitragem: nao ha' acao possivel do nosso lado.
 * 3. `isQuickRefund = true` significa que o TikTok ja' reembolsou por conta
 *    propria e NAO podemos rejeitar o pacote recebido.
 *
 * `createTime`/`updateTime` sao epoch em SEGUNDOS.
 *
 * @property list<SellerNextAction>|null $sellerNextActionResponse
 * @property list<ReturnLineItem>|null $returnLineItems
 * @property list<ReturnSubLineItem>|null $returnSubLineItems
 * @property list<ExchangeOrderLine>|null $exchangeOrderLines
 */
final class SkuReturnRequest implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Pedido de VENDA. Chave do de/para com `orders`. */
        public readonly ?string $orderId = null,
        public readonly ?string $returnId = null,
        /** REFUND | RETURN_AND_REFUND | REPLACEMENT | EXCHANGE */
        public readonly ?string $returnType = null,
        /** RETURN_OR_REFUND_REQUEST_PENDING | ... | REFUND_SUCCESS */
        public readonly ?string $returnStatus = null,
        /** IN_PROGRESS | ... — arbitragem do TikTok tira a decisao da nossa mao. */
        public readonly ?string $arbitrationStatus = null,
        /** BUYER | SELLER | OPERATOR (OPERATOR = TikTok) */
        public readonly ?string $role = null,
        /** Chave i18n do motivo; `returnReasonText` e' so' o rotulo traduzido. */
        public readonly ?string $returnReason = null,
        public readonly ?string $returnReasonText = null,
        #[ArrayOf(SellerNextAction::class)]
        public readonly ?array $sellerNextActionResponse = null,
        /** PLATFORM | BUYER_ARRANGE */
        public readonly ?string $shipmentType = null,
        /** DROP_OFF | PICKUP */
        public readonly ?string $handoverMethod = null,
        public readonly ?string $returnTrackingNumber = null,
        public readonly ?string $returnProviderName = null,
        public readonly ?string $returnProviderId = null,
        // Devolucao editada gera uma NOVA return: a cadeia vive nestes dois ids.
        public readonly ?string $preReturnId = null,
        public readonly ?string $nextReturnId = null,
        public readonly ?RefundAmount $refundAmount = null,
        #[ArrayOf(ReturnLineItem::class)]
        public readonly ?array $returnLineItems = null,
        // Epoch em SEGUNDOS.
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?RefundDiscount $discount = null,
        public readonly ?ReturnShippingFee $shippingFee = null,
        /** Comprador fica com o produto mesmo sendo reembolsado. */
        public readonly ?bool $buyerKeepItem = null,
        /** SHIPPING_LABEL | QR_CODE ... */
        public readonly ?string $returnShippingDocumentType = null,
        /** SELLER_SHIPPED | BUYER_SHIPPED | SELLER_ARRANGE ... */
        public readonly ?string $returnMethod = null,
        public readonly ?bool $isCombinedReturn = null,
        public readonly ?string $combinedReturnId = null,
        /** PARTIAL_REFUND — proposta nossa de reembolso parcial. */
        public readonly ?string $sellerProposedReturnType = null,
        public readonly ?PartialRefundAmount $partialRefundAmount = null,
        public readonly ?bool $buyerRejectedPartialRefund = null,
        public readonly ?ReturnWarehouseAddress $returnWarehouseAddress = null,
        #[ArrayOf(ExchangeOrderLine::class)]
        public readonly ?array $exchangeOrderLines = null,
        /** "Ignore this field" na doc — mapeado pra nao perder dado futuro. */
        public readonly ?string $reshipmentOrderId = null,
        public readonly ?bool $isQuickRefund = null,
        #[ArrayOf(ReturnSubLineItem::class)]
        public readonly ?array $returnSubLineItems = null,
    ) {}
}
