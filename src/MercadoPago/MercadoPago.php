<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\AdvancedPayments\AdvancedPaymentsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\AuthorizedPayments\AuthorizedPaymentsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\CardTokens\CardTokensMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Catalog\CatalogMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Chargebacks\ChargebacksMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Customers\CustomersMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\MerchantOrders\MerchantOrdersMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Orders\OrdersMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Payments\PaymentsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Point\PointMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\PreApprovalPlans\PreApprovalPlansMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\PreApprovals\PreApprovalsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Preferences\PreferencesMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Refunds\RefundsMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Settlement\SettlementMethods;
use SistemAtc\Marketplaces\MercadoPago\Endpoints\Users\UsersMethods;
use SistemAtc\Marketplaces\MercadoPago\Support\HttpClientFactory;

/**
 * Ponto de entrada do Mercado Pago. Cobre os mesmos recursos do SDK oficial
 * (mercadopago/sdk-php) mais os relatorios de liquidacao (settlement), que
 * o SDK oficial nao tem. OAuth sem integration: `Support\OAuth`.
 */
class MercadoPago
{
    public function payments(MarketplaceIntegration $integration): PaymentsMethods
    {
        return new PaymentsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function refunds(MarketplaceIntegration $integration): RefundsMethods
    {
        return new RefundsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function settlement(MarketplaceIntegration $integration): SettlementMethods
    {
        return new SettlementMethods(HttpClientFactory::make($integration), $integration);
    }

    public function chargebacks(MarketplaceIntegration $integration): ChargebacksMethods
    {
        return new ChargebacksMethods(HttpClientFactory::make($integration), $integration);
    }

    public function merchantOrders(MarketplaceIntegration $integration): MerchantOrdersMethods
    {
        return new MerchantOrdersMethods(HttpClientFactory::make($integration), $integration);
    }

    public function preferences(MarketplaceIntegration $integration): PreferencesMethods
    {
        return new PreferencesMethods(HttpClientFactory::make($integration), $integration);
    }

    public function customers(MarketplaceIntegration $integration): CustomersMethods
    {
        return new CustomersMethods(HttpClientFactory::make($integration), $integration);
    }

    public function cardTokens(MarketplaceIntegration $integration): CardTokensMethods
    {
        return new CardTokensMethods(HttpClientFactory::make($integration), $integration);
    }

    public function catalog(MarketplaceIntegration $integration): CatalogMethods
    {
        return new CatalogMethods(HttpClientFactory::make($integration), $integration);
    }

    public function orders(MarketplaceIntegration $integration): OrdersMethods
    {
        return new OrdersMethods(HttpClientFactory::make($integration), $integration);
    }

    public function preApprovals(MarketplaceIntegration $integration): PreApprovalsMethods
    {
        return new PreApprovalsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function preApprovalPlans(MarketplaceIntegration $integration): PreApprovalPlansMethods
    {
        return new PreApprovalPlansMethods(HttpClientFactory::make($integration), $integration);
    }

    public function authorizedPayments(MarketplaceIntegration $integration): AuthorizedPaymentsMethods
    {
        return new AuthorizedPaymentsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function point(MarketplaceIntegration $integration): PointMethods
    {
        return new PointMethods(HttpClientFactory::make($integration), $integration);
    }

    public function advancedPayments(MarketplaceIntegration $integration): AdvancedPaymentsMethods
    {
        return new AdvancedPaymentsMethods(HttpClientFactory::make($integration), $integration);
    }

    public function users(MarketplaceIntegration $integration): UsersMethods
    {
        return new UsersMethods(HttpClientFactory::make($integration), $integration);
    }
}
