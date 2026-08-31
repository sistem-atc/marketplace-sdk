<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Ads\Endpoints\Reporting\ReportingMethods as AdsReportingMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Analytics\AnalyticsMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliateCreator\AffiliateCreatorMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliateSeller\AffiliateSellerMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliatePartner\AffiliatePartnerMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Authorization\AuthorizationMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerEngagement\CustomerEngagementMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerService\CustomerServiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Event\EventMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Fbt\FbtMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Finance\FinanceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Fulfillment\FulfillmentMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Open\OpenMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Product\ProductMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Reverse\ReverseMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\Seller\SellerMethods;
use SistemAtc\Marketplaces\Tiktok\Endpoints\SupplyChain\SupplyChainMethods;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

class Tiktok
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Affiliate Creator API — lado CREATOR do programa de afiliados (vitrine,
     * colaboracoes, comissoes, videos/fotos compraveis e AMOSTRAS).
     *
     * ⚠️ Exige token de CREATOR (user_type=1) e integration SEM shop_cipher —
     * nao e' o token de loja usado em orders()/products().
     */
    public function affiliateCreator(MarketplaceIntegration $integration): AffiliateCreatorMethods
    {
        return new AffiliateCreatorMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Affiliate Seller API — colaboracoes com creators, amostras, IM e os
     * PEDIDOS DE AFILIADO, que abrem a comissao agregada do extrato
     * (`affiliate_commission_amount`) por creator, colaboracao e taxa.
     */
    public function affiliateSeller(MarketplaceIntegration $integration): AffiliateSellerMethods
    {
        return new AffiliateSellerMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Authorization API — lojas autorizadas + shop_cipher (pos-OAuth). */
    public function authorization(MarketplaceIntegration $integration): AuthorizationMethods
    {
        return new AuthorizationMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Analytics API — relatorios de desempenho da LOJA e da audiencia
     * (GMV/trafego/conversao, lives, videos, produtos, SKUs e SPS).
     *
     * NAO confundir com `ads()`: aquilo e' a Marketing API (outro host, outro
     * token, gasto de anuncio). Aqui e' o host normal do Shop, com HMAC +
     * shop_cipher da propria integration.
     */
    public function analytics(MarketplaceIntegration $integration): AnalyticsMethods
    {
        return new AnalyticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function finance(MarketplaceIntegration $integration): FinanceMethods
    {
        return new FinanceMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Fulfillment API — pacotes, despacho, etiquetas e UPLOAD DE NOTA FISCAL
     * (`/fulfillment/202502/invoice/upload`). O acessor `invoice()` abaixo
     * aponta pra `/finance/.../invoices`, caminho que nao existe.
     */
    public function fulfillment(MarketplaceIntegration $integration): FulfillmentMethods
    {
        return new FulfillmentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoice(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * FBT (Fulfilled by TikTok) — fulfillment do proprio canal, equivalente ao
     * Full do ML e ao FBA da Amazon. Cobre goods, inventario, inbound e MCF.
     *
     * Nao e' so' logistica: venda por fulfillment exige DUAS notas (remessa ao
     * armazem de terceiro + venda), e os endpoints daqui sao a contrapartida
     * fisica dessas notas.
     */
    public function fbt(MarketplaceIntegration $integration): FbtMethods
    {
        return new FbtMethods(HttpClientFactory::make($integration), $integration);
    }

    public function products(MarketplaceIntegration $integration): ProductMethods
    {
        return new ProductMethods(HttpClientFactory::make($integration), $integration);
    }

    public function category(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function promotion(MarketplaceIntegration $integration): PromotionMethods
    {
        return new PromotionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function reverse(MarketplaceIntegration $integration): ReverseMethods
    {
        return new ReverseMethods(HttpClientFactory::make($integration), $integration);
    }

    public function customerService(MarketplaceIntegration $integration): CustomerServiceMethods
    {
        return new CustomerServiceMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Customer Engagement API — disparo de mensagem em MASSA pra base de
     * compradores (retencao). Cai no mesmo inbox do chat, mas e' outra API:
     * o historico das conversas continua vindo de customerService().
     */
    public function customerEngagement(MarketplaceIntegration $integration): CustomerEngagementMethods
    {
        return new CustomerEngagementMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Affiliate Partner API — lado da AGENCIA (TAP/CAP): campanhas, aprovacao
     * de produto, links pros criadores e pedidos de afiliado. Exige
     * `category_asset_cipher` (identificador do PARCEIRO), que e' diferente do
     * `shop_cipher` da loja.
     */
    public function affiliatePartner(MarketplaceIntegration $integration): AffiliatePartnerMethods
    {
        return new AffiliatePartnerMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Supply Chain API — canal do WMS/3PL (identificado por
     * `warehouse_provider_id`), NAO o do vendedor. Pra despachar como
     * vendedor, o caminho e' fulfillment().
     */
    public function supplyChain(MarketplaceIntegration $integration): SupplyChainMethods
    {
        return new SupplyChainMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Familia `/open/...` — utilitarios transversais (hoje, abertura de upload
     * de arquivo em chunks). Sem dominio de negocio proprio.
     */
    public function open(MarketplaceIntegration $integration): OpenMethods
    {
        return new OpenMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Event API — listar/repontar/remover os webhooks da loja pela API. */
    public function event(MarketplaceIntegration $integration): EventMethods
    {
        return new EventMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Seller API — permissões cross-border e grupo de lojas do VENDEDOR. */
    public function seller(MarketplaceIntegration $integration): SellerMethods
    {
        return new SellerMethods(HttpClientFactory::make($integration), $integration);
    }

    /**
     * Marketing API (business-api.tiktok.com) — relatórios de gasto de ads
     * (GMV Max + auction). A integration aqui é a de ADS (token de longa
     * duração do portal TikTok for Business), NÃO a do Shop: outro host,
     * auth por header `Access-Token`, sem assinatura HMAC/shop_cipher.
     */
    public function ads(MarketplaceIntegration $integration): AdsReportingMethods
    {
        return new AdsReportingMethods($integration);
    }
}
