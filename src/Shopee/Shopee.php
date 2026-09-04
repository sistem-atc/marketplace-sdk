<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Shopee\Endpoints\Ads\AdsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Payment\PaymentMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Webhook\WebhookMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Return\ReturnMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Chat\ChatMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Marketing\MarketingMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Shopee\Endpoints\Livestream\LivestreamMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Video\VideoMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\AddOnDeal\AddOnDealMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\BundleDeal\BundleDealMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\FlashSale\FlashSaleMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\FollowPrize\FollowPrizeMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\TopPicks\TopPicksMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\GlobalProduct\GlobalProductMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Merchant\MerchantMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Principal\PrincipalMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\PublicApi\PublicMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\ShopCategory\ShopCategoryMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\MediaSpace\MediaSpaceMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Media\MediaMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\FirstMile\FirstMileMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Fbs\FbsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Shop\ShopMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\AccountHealth\AccountHealthMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Sbs\SbsMethods;
use SistemAtc\Marketplaces\Shopee\Endpoints\Ams\AmsMethods;

class Shopee
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function payment(MarketplaceIntegration $integration): PaymentMethods
    {
        return new PaymentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Catalogo de categorias e atributos (equivalente ao /categories do ML). */
    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function returns(MarketplaceIntegration $integration): ReturnMethods
    {
        return new ReturnMethods(HttpClientFactory::make($integration), $integration);
    }

    public function chat(MarketplaceIntegration $integration): ChatMethods
    {
        return new ChatMethods(HttpClientFactory::make($integration), $integration);
    }

    public function marketing(MarketplaceIntegration $integration): MarketingMethods
    {
        return new MarketingMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Publicidade CPC (`/api/v2/ads`) — gasto diário da loja. Não confundir com
     * marketing(), que é desconto/voucher.
     */
    public function ads(MarketplaceIntegration $integration): AdsMethods
    {
        return new AdsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function livestream(MarketplaceIntegration $integration): LivestreamMethods
    {
        return new LivestreamMethods(HttpClientFactory::make($integration), $integration);
    }

    public function video(MarketplaceIntegration $integration): VideoMethods
    {
        return new VideoMethods(HttpClientFactory::make($integration), $integration);
    }

    public function addOnDeal(MarketplaceIntegration $integration): AddOnDealMethods
    {
        return new AddOnDealMethods(HttpClientFactory::make($integration), $integration);
    }

    public function bundleDeal(MarketplaceIntegration $integration): BundleDealMethods
    {
        return new BundleDealMethods(HttpClientFactory::make($integration), $integration);
    }

    public function flashSale(MarketplaceIntegration $integration): FlashSaleMethods
    {
        return new FlashSaleMethods(HttpClientFactory::make($integration), $integration);
    }

    public function followPrize(MarketplaceIntegration $integration): FollowPrizeMethods
    {
        return new FollowPrizeMethods(HttpClientFactory::make($integration), $integration);
    }

    public function topPicks(MarketplaceIntegration $integration): TopPicksMethods
    {
        return new TopPicksMethods(HttpClientFactory::make($integration), $integration);
    }

    public function globalProducts(MarketplaceIntegration $integration): GlobalProductMethods
    {
        return new GlobalProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function merchant(MarketplaceIntegration $integration): MerchantMethods
    {
        return new MerchantMethods(HttpClientFactory::make($integration), $integration);
    }

    public function principal(MarketplaceIntegration $integration): PrincipalMethods
    {
        return new PrincipalMethods(HttpClientFactory::make($integration), $integration);
    }

    public function publicApi(MarketplaceIntegration $integration): PublicMethods
    {
        return new PublicMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shopCategories(MarketplaceIntegration $integration): ShopCategoryMethods
    {
        return new ShopCategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function mediaSpace(MarketplaceIntegration $integration): MediaSpaceMethods
    {
        return new MediaSpaceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function media(MarketplaceIntegration $integration): MediaMethods
    {
        return new MediaMethods(HttpClientFactory::make($integration), $integration);
    }

    public function firstMile(MarketplaceIntegration $integration): FirstMileMethods
    {
        return new FirstMileMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fbs(MarketplaceIntegration $integration): FbsMethods
    {
        return new FbsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function shop(MarketplaceIntegration $integration): ShopMethods
    {
        return new ShopMethods(HttpClientFactory::make($integration), $integration);
    }

    public function accountHealth(MarketplaceIntegration $integration): AccountHealthMethods
    {
        return new AccountHealthMethods(HttpClientFactory::make($integration), $integration);
    }

    public function sbs(MarketplaceIntegration $integration): SbsMethods
    {
        return new SbsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function ams(MarketplaceIntegration $integration): AmsMethods
    {
        return new AmsMethods(HttpClientFactory::make($integration), $integration);
    }
}
