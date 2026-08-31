<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Enums;

/**
 * Os 44 type codes oficiais de webhook do TikTok Shop.
 *
 * O TikTok NAO manda o nome do topico no payload — so' o inteiro `type`. Sem
 * este mapa o roteamento vira numero magico espalhado pelo consumidor (era
 * exatamente o estado do conector: 1, 4, 33, 36 hardcoded em match()).
 *
 * A numeracao e' esparsa e NAO e' sequencial (nao existem 2, 8, 9, 10, 26,
 * 28-32, 34, 40, 41, 43-45, 47-49, 53, 54, 57, 60, 61) — provavelmente topicos
 * descontinuados ou internos. Nao preencha os buracos por simetria.
 *
 * ⚠️ NUNCA use `WebhookEventType::from()` no controller de webhook: o TikTok
 * adiciona topico novo sem aviso e `from()` estoura ValueError — o payload se
 * perderia depois de ja' termos respondido (ou pior, devolveriamos 500 e o
 * TikTok reentregaria pra sempre). Use `tryFrom()` / `isKnown()`, grave o cru,
 * e trate o desconhecido como no-op.
 */
enum WebhookEventType: int
{
    case ORDER_STATUS_CHANGE = 1;
    case RECIPIENT_ADDRESS_UPDATE = 3;
    case PACKAGE_UPDATE = 4;
    case PRODUCT_STATUS_CHANGE = 5;
    case SELLER_DEAUTHORIZATION = 6;
    case UPCOMING_AUTHORIZATION_EXPIRATION = 7;
    case CANCELLATION_STATUS_CHANGE = 11;
    case RETURN_STATUS_CHANGE = 12;
    case NEW_CONVERSATION = 13;
    case NEW_MESSAGE = 14;
    case PRODUCT_INFORMATION_CHANGE = 15;
    case PRODUCT_CREATION = 16;
    case SHOPPABLE_CONTENT_POSTING = 17;
    case PRODUCT_CATEGORY_CHANGE = 18;
    case SIZE_CHART_CHANGE = 19;
    case CREATOR_DEAUTHORIZATION = 20;
    case INBOUND_FBT_ORDER_STATUS_CHANGE = 21;
    case FBT_MERCHANT_ONBOARDING = 22;
    case GOODS_MATCH = 23;
    case FBT_INVENTORY_UPDATE = 24;
    case OPPORTUNITY_MATCHING_STATUS_CHANGE = 25;
    case INVENTORY_STATUS_CHANGE = 27;
    case NEW_MESSAGE_LISTENER = 33;
    case TOKOPEDIA_MIRROR_STATUS_CHANGE = 35;
    case INVOICE_STATUS_CHANGE = 36;
    case PRODUCT_AUDIT_STATUS_CHANGE = 37;
    case STRIKETHROUGH_PRICE_EXPIRED = 38;
    case ACTIVITY_STATUS_CHANGE = 39;
    case COMBINED_LISTING_CHANGE = 42;
    case IMAGE_TRANSLATION_COMPLETED = 46;
    case SKU_STATUS_CHANGE = 50;
    case GLOBAL_REPLICATION_STATUS_CHANGE = 51;
    case GLOBAL_LISTING_METHOD_CHANGE = 52;
    case VIDEO_PRECHECK_RESULT = 55;
    case SAMPLE_APPLICATION_STATUS_CHANGE = 56;
    case FBT_MCF_ORDER_STATUS = 58;
    case SHOPPABLE_VIDEO_PRECHECK_TASKS_RESULT = 59;
    case PRODUCT_PACKAGE_RECOMMENDED = 62;
    case ACTIVITY_CHANGE = 63;
    case AFTERSALES_REQUEST_STATUS_UPDATE = 64;
    case RMA_STATUS_UPDATE = 65;
    case APPEAL_COMPLETED = 66;
    case REFUND_SUCCESS = 67;
    case INVENTORY_CHANGED = 68;

    /** Nome legivel do topico, como na doc oficial. Serve pra log e pra tela. */
    public function label(): string
    {
        return match ($this) {
            self::ORDER_STATUS_CHANGE => 'Order status change',
            self::RECIPIENT_ADDRESS_UPDATE => 'Recipient address update',
            self::PACKAGE_UPDATE => 'Package update',
            self::PRODUCT_STATUS_CHANGE => 'Product status change',
            self::SELLER_DEAUTHORIZATION => 'Seller deauthorization',
            self::UPCOMING_AUTHORIZATION_EXPIRATION => 'Upcoming authorization expiration',
            self::CANCELLATION_STATUS_CHANGE => 'Cancellation status change',
            self::RETURN_STATUS_CHANGE => 'Return status change',
            self::NEW_CONVERSATION => 'New conversation',
            self::NEW_MESSAGE => 'New message',
            self::PRODUCT_INFORMATION_CHANGE => 'Product information change',
            self::PRODUCT_CREATION => 'Product creation',
            self::SHOPPABLE_CONTENT_POSTING => 'Shoppable content posting',
            self::PRODUCT_CATEGORY_CHANGE => 'Product category change',
            self::SIZE_CHART_CHANGE => 'Size chart change',
            self::CREATOR_DEAUTHORIZATION => 'Creator deauthorization',
            self::INBOUND_FBT_ORDER_STATUS_CHANGE => 'Inbound FBT order status change',
            self::FBT_MERCHANT_ONBOARDING => 'FBT merchant onboarding',
            self::GOODS_MATCH => 'Goods match',
            self::FBT_INVENTORY_UPDATE => 'FBT inventory update',
            self::OPPORTUNITY_MATCHING_STATUS_CHANGE => 'Opportunity matching status change',
            self::INVENTORY_STATUS_CHANGE => 'Inventory status change',
            self::NEW_MESSAGE_LISTENER => 'New message listener',
            self::TOKOPEDIA_MIRROR_STATUS_CHANGE => 'Tokopedia mirror status change',
            self::INVOICE_STATUS_CHANGE => 'Invoice status change',
            self::PRODUCT_AUDIT_STATUS_CHANGE => 'Product audit status change',
            self::STRIKETHROUGH_PRICE_EXPIRED => 'Strikethrough price expired',
            self::ACTIVITY_STATUS_CHANGE => 'Activity status change',
            self::COMBINED_LISTING_CHANGE => 'Combined listing change',
            self::IMAGE_TRANSLATION_COMPLETED => 'Image translation completed',
            self::SKU_STATUS_CHANGE => 'SKU status change',
            self::GLOBAL_REPLICATION_STATUS_CHANGE => 'Global replication status change',
            self::GLOBAL_LISTING_METHOD_CHANGE => 'Global listing method change',
            self::VIDEO_PRECHECK_RESULT => 'Video Precheck Result',
            self::SAMPLE_APPLICATION_STATUS_CHANGE => 'Sample Application Status Change',
            self::FBT_MCF_ORDER_STATUS => 'Get FBT MCF Order Status',
            self::SHOPPABLE_VIDEO_PRECHECK_TASKS_RESULT => 'Shoppable Video Precheck Tasks Result',
            self::PRODUCT_PACKAGE_RECOMMENDED => 'Product Package Recommended',
            self::ACTIVITY_CHANGE => 'Activity change',
            self::AFTERSALES_REQUEST_STATUS_UPDATE => 'Aftersales Request Status Update',
            self::RMA_STATUS_UPDATE => 'RMA Status Update',
            self::APPEAL_COMPLETED => 'Appeal Completed Webhook',
            self::REFUND_SUCCESS => 'Refund Success',
            self::INVENTORY_CHANGED => 'Inventory changed',
        };
    }

    /**
     * O TikTok manda um topico que este SDK ainda nao conhece? Pergunte aqui
     * ANTES de rotear — e' o que impede o webhook de explodir num type novo.
     */
    public static function isKnown(int $type): bool
    {
        return self::tryFrom($type) instanceof self;
    }
}
