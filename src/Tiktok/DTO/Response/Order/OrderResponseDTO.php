<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido TikTok Shop — item de `data.orders[]` do `/order/{v}/orders`.
 *
 * TRÊS COISAS QUE DIFEREM DOS OUTROS MPs:
 *
 * 1. DINHEIRO É STRING ("45.90", "0.00"). Tipar float quebraria o roundtrip
 *    lossless e comeria a casa decimal. Converta no consumidor.
 * 2. NÃO HÁ QUANTIDADE: cada `lineItems[]` é UMA unidade — 2 unidades do
 *    mesmo SKU = 2 entradas. Agrupe por `skuId` pra contar.
 * 3. IDs são STRING (não int) — `id`, `userId`, `productId`, `skuId`.
 *    São snowflake IDs grandes; tratar como número perde precisão.
 *
 * Datas são epoch em SEGUNDOS.
 *
 * PII: `cpf`/`cpfName` vêm na RAIZ (não dentro de recipientAddress), e o
 * TikTok BR os entrega sem máscara — diferente da Shopee.
 *
 * @property list<OrderLineItem>|null $lineItems
 * @property list<OrderPackage>|null $packages
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // IDs: STRING (snowflake).
        public readonly ?string $id = null,
        public readonly ?string $status = null,
        public readonly ?string $orderType = null,
        public readonly ?string $userId = null,
        // Comprador / PII (na raiz, sem máscara no BR)
        public readonly ?string $buyerEmail = null,
        public readonly ?string $buyerMessage = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cpfName = null,
        public readonly ?string $channelEntityNationalRegistryId = null,
        public readonly ?RecipientAddress $recipientAddress = null,
        // Datas: epoch em SEGUNDOS
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        public readonly ?int $paidTime = null,
        public readonly ?int $deliveryTime = null,
        public readonly ?int $collectionTime = null,
        public readonly ?int $cancelTime = null,
        public readonly ?int $rtsTime = null,
        // SLAs (epoch em SEGUNDOS)
        public readonly ?int $cancelOrderSlaTime = null,
        public readonly ?int $collectionDueTime = null,
        public readonly ?int $rtsSlaTime = null,
        public readonly ?int $ttsSlaTime = null,
        public readonly ?int $deliveryDueTime = null,
        public readonly ?int $deliverySlaTime = null,
        public readonly ?int $shippingDueTime = null,
        // Logística
        public readonly ?string $deliveryOptionId = null,
        public readonly ?string $deliveryOptionName = null,
        public readonly ?string $deliveryType = null,
        // fulfillment_type distingue FBT (TikTok cuida) de seller-fulfilled.
        public readonly ?string $fulfillmentType = null,
        public readonly ?string $shippingProvider = null,
        public readonly ?string $shippingProviderId = null,
        public readonly ?string $shippingType = null,
        public readonly ?string $trackingNumber = null,
        public readonly ?string $warehouseId = null,
        // Pagamento
        public readonly ?OrderPayment $payment = null,
        public readonly ?string $paymentMethodName = null,
        public readonly ?string $paymentMethodCode = null,
        public readonly ?string $paymentAuthCode = null,
        public readonly ?string $paymentCardType = null,
        // Cancelamento
        public readonly ?string $cancelReason = null,
        public readonly ?string $cancellationInitiator = null,
        // Flags
        public readonly ?bool $isCod = null,
        public readonly ?bool $isOnHoldOrder = null,
        public readonly ?bool $isReplacementOrder = null,
        public readonly ?bool $isSampleOrder = null,
        public readonly ?bool $hasUpdatedRecipientAddress = null,
        // STRING, não bool: o TikTok manda "yes"/"no" aqui.
        public readonly ?string $needUploadInvoice = null,
        public readonly ?string $commercePlatform = null,
        // ── Campos que o DTO descartava em silencio ────────────────────────
        // O teste de roundtrip passava porque comparava contra um fixture
        // escrito a mao: campo ausente do fixture nunca era cobrado. Estes 24
        // vinham da API e morriam na desserializacao.

        /**
         * AMOSTRA REEMBOLSAVEL: o comprador PAGA e o TikTok reembolsa depois
         * (programa de creators). Diferente de `orderType =
         * SELLER_FUND_FREE_SAMPLE`, onde o comprador paga zero.
         *
         * E' o unico sinal que distingue esse pedido de uma venda normal — sem
         * ele o financeiro espera um repasse que nao vem.
         */
        public readonly ?bool $isRefundableSample = null,

        // Troca/substituicao: o pedido novo aponta o que ele substitui.
        public readonly ?bool $isExchangeOrder = null,
        public readonly ?string $exchangeSourceOrderId = null,
        public readonly ?string $replacedOrderId = null,

        public readonly ?bool $isSubscriptionOrder = null,
        public readonly ?bool $isBuyerRequestCancel = null,
        public readonly ?bool $authenticationRequired = null,

        /** COMBINED | SPLIT — pedido combinado ou dividido pelo canal. */
        public readonly ?string $splitOrCombineTag = null,
        public readonly ?string $autoCombineGroupId = null,

        public readonly ?HandlingDuration $handlingDuration = null,

        /** @var list<int>|null */
        public readonly ?array $orderRights = null,

        public readonly ?string $sellerNote = null,
        public readonly ?string $consultationId = null,
        public readonly ?string $fastDeliveryProgram = null,
        public readonly ?int $fulfillmentPriorityLevel = null,

        // Comprador (avatar/apelido publicos, nao PII sensivel).
        public readonly ?string $buyerNickname = null,
        public readonly ?string $buyerAvatar = null,

        // Datas — epoch em SEGUNDOS, como o resto do DTO.
        public readonly ?int $requestCancelTime = null,
        public readonly ?int $releaseDate = null,
        public readonly ?int $pickUpCutOffTime = null,
        public readonly ?int $fastDispatchSlaTime = null,
        public readonly ?int $recommendedShippingTime = null,
        public readonly ?int $instorePickupSlaTime = null,
        public readonly ?int $deliveryOptionRequiredDeliveryTime = null,

        #[ArrayOf(OrderLineItem::class)]
        public readonly ?array $lineItems = null,
        #[ArrayOf(OrderPackage::class)]
        public readonly ?array $packages = null,
    ) {}
}
