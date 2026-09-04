<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Publication.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class PublicationMutations extends BaseOperations
{
    /**
     * Creates a [`Publication`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Publication) that controls which [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) and
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: PublicationCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publicationCreate(array $args = [], string $selection = 'publication { autoPublish hasCollection id name supportsFuturePublishing } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publicationCreate', $args, ['input' => 'PublicationCreateInput!'], $selection);
    }

    /**
     * Deletes a publication.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publicationDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publicationDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a [`Publication`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Publication). You can add or remove [products](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) f
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: PublicationUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publicationUpdate(array $args = [], string $selection = 'publication { autoPublish hasCollection id name supportsFuturePublishing } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publicationUpdate', $args, ['id' => 'ID!', 'input' => 'PublicationUpdateInput!'], $selection);
    }

    /**
     * Publishes a resource, such as a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) or [`Collection`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection),
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: [PublicationInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publishablePublish(array $args = [], string $selection = 'publishable { publishedOnCurrentChannel publishedOnCurrentPublication } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publishablePublish', $args, ['id' => 'ID!', 'input' => '[PublicationInput!]!'], $selection);
    }

    /**
     * Publishes a resource to the current [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) associated with the requesting app. The system determines the current channel by the
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publishablePublishToCurrentChannel(array $args = [], string $selection = 'publishable { publishedOnCurrentChannel publishedOnCurrentPublication } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publishablePublishToCurrentChannel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Unpublishes a resource, such as a [`Product`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) or [`Collection`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection)
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: [PublicationInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publishableUnpublish(array $args = [], string $selection = 'publishable { publishedOnCurrentChannel publishedOnCurrentPublication } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publishableUnpublish', $args, ['id' => 'ID!', 'input' => '[PublicationInput!]!'], $selection);
    }

    /**
     * Unpublishes a resource from the current channel. If the resource is a product, then it's visible in the channel only if the product status is `active`.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function publishableUnpublishToCurrentChannel(array $args = [], string $selection = 'publishable { publishedOnCurrentChannel publishedOnCurrentPublication } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'publishableUnpublishToCurrentChannel', $args, ['id' => 'ID!'], $selection);
    }
}
