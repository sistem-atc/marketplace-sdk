<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL;

use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * Hub da Shopify Admin API GraphQL: `->client()` (documento livre + paginate),
 * `->queries()-><dominio>()` e `->mutations()-><dominio>()` (metodos gerados,
 * um por query/mutation do schema 2026-07).
 *
 * Uso: `MarketPlaces::Shopify()->graphql($integration)->queries()->orders()->orders(['first' => 50])`.
 */
final class Admin
{
    public function __construct(private GraphQLClient $client) {}

    public static function forIntegration(MarketplaceIntegration $integration): self
    {
        return new self(GraphQLClient::forIntegration($integration));
    }

    public function client(): GraphQLClient
    {
        return $this->client;
    }

    public function queries(): QueriesRegistry
    {
        return new QueriesRegistry($this->client);
    }

    public function mutations(): MutationsRegistry
    {
        return new MutationsRegistry($this->client);
    }
}
