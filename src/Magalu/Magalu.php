<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Magalu\Endpoints\Order\OrderMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Invoice\InvoiceMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Delivery\DeliveryMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Logistics\LogisticsMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Webhooks\WebhookMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\CategoryMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Product\PortfolioMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Claim\ClaimMethods;
use SistemAtc\Marketplaces\Magalu\Support\HttpClientFactory;
use SistemAtc\Marketplaces\Magalu\Endpoints\Promotion\PromotionMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\PricingRule\PricingRuleMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\FinancialAnalysis\FinancialAnalysisMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Cardan\CardanMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Sac\SacMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Conversation\ConversationMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Conversation\QuestionMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Carrier\CarrierMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Seller\SellerMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FulfillmentMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FiscalManagementMethods;
use SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment\FiscalDocumentMethods;

class Magalu
{
    public function orders(MarketplaceIntegration $integration): OrderMethods
    {
        return new OrderMethods(HttpClientFactory::make($integration), $integration);
    }

    public function invoices(MarketplaceIntegration $integration): InvoiceMethods
    {
        return new InvoiceMethods(HttpClientFactory::make($integration), $integration);
    }

    public function deliveries(MarketplaceIntegration $integration): DeliveryMethods
    {
        return new DeliveryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function logistics(MarketplaceIntegration $integration): LogisticsMethods
    {
        return new LogisticsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function webhooks(MarketplaceIntegration $integration): WebhookMethods
    {
        return new WebhookMethods(HttpClientFactory::make($integration), $integration);
    }

    public function portfolio(MarketplaceIntegration $integration): PortfolioMethods
    {
        return new PortfolioMethods(HttpClientFactory::make($integration), $integration);
    }

    /** Categorias e atributos (o que define os campos exigidos do SKU). */
    public function categories(MarketplaceIntegration $integration): CategoryMethods
    {
        return new CategoryMethods(HttpClientFactory::make($integration), $integration);
    }

    public function claims(MarketplaceIntegration $integration): ClaimMethods
    {
        return new ClaimMethods(HttpClientFactory::make($integration), $integration);
    }

    public function promotions(MarketplaceIntegration $integration): PromotionMethods
    {
        return new PromotionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function pricingRules(MarketplaceIntegration $integration): PricingRuleMethods
    {
        return new PricingRuleMethods(HttpClientFactory::make($integration), $integration);
    }

    public function financialAnalysis(MarketplaceIntegration $integration): FinancialAnalysisMethods
    {
        return new FinancialAnalysisMethods(HttpClientFactory::make($integration), $integration);
    }

    public function cardan(MarketplaceIntegration $integration): CardanMethods
    {
        return new CardanMethods(HttpClientFactory::make($integration), $integration);
    }

    public function sac(MarketplaceIntegration $integration): SacMethods
    {
        return new SacMethods(HttpClientFactory::make($integration), $integration);
    }

    public function conversations(MarketplaceIntegration $integration): ConversationMethods
    {
        return new ConversationMethods(HttpClientFactory::make($integration), $integration);
    }

    public function questions(MarketplaceIntegration $integration): QuestionMethods
    {
        return new QuestionMethods(HttpClientFactory::make($integration), $integration);
    }

    public function carrier(MarketplaceIntegration $integration): CarrierMethods
    {
        return new CarrierMethods(HttpClientFactory::make($integration), $integration);
    }

    public function seller(MarketplaceIntegration $integration): SellerMethods
    {
        return new SellerMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fulfillment(MarketplaceIntegration $integration): FulfillmentMethods
    {
        return new FulfillmentMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fiscalManagement(MarketplaceIntegration $integration): FiscalManagementMethods
    {
        return new FiscalManagementMethods(HttpClientFactory::make($integration), $integration);
    }

    public function fiscalDocuments(MarketplaceIntegration $integration): FiscalDocumentMethods
    {
        return new FiscalDocumentMethods(HttpClientFactory::make($integration), $integration);
    }
}
