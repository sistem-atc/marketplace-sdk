<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Collection.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CollectionMutations extends BaseOperations
{
    /**
     * Adds multiple products to an existing collection in a single operation. This mutation provides an efficient way to bulk-manage collection membership without individual product updates. For example, w
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionAddProducts(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionAddProducts', $args, ['id' => 'ID!', 'productIds' => '[ID!]!'], $selection);
    }

    /**
     * Adds products to a [`Collection`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection) asynchronously and returns a [`Job`](https://shopify.dev/docs/api/admin-graphql/latest/objects/J
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionAddProductsV2(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionAddProductsV2', $args, ['id' => 'ID!', 'productIds' => '[ID!]!'], $selection);
    }

    /**
     * Creates a shareable collection source that can later be linked to one or more collections.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionCreateConditionsSourceInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionsSourceCreate(array $args = [], string $selection = 'source { description id shareable targetType title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionConditionsSourceCreate', $args, ['input' => 'CollectionCreateConditionsSourceInput!'], $selection);
    }

    /**
     * Deletes a shareable collection source owned by the calling app.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionsSourceDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionConditionsSourceDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a shareable collection source owned by the calling app.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionUpdateConditionsSourceInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionConditionsSourceUpdate(array $args = [], string $selection = 'source { description id shareable targetType title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionConditionsSourceUpdate', $args, ['input' => 'CollectionUpdateConditionsSourceInput!'], $selection);
    }

    /**
     * Creates a [collection](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection) to group [products](https://shopify.dev/docs/api/admin-graphql/latest/objects/Product) together in the [onl
     *
     * @param array<string,mixed> $args Variaveis GraphQL: collection: CollectionCreateInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionCreate(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionCreate', $args, ['collection' => 'CollectionCreateInput'], $selection);
    }

    /**
     * Deletes a collection and removes it permanently from the store. This operation cannot be undone and will remove the collection from all sales channels where it was published. For example, when mercha
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionDelete(array $args = [], string $selection = 'deletedCollectionId shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionDelete', $args, ['input' => 'CollectionDeleteInput!'], $selection);
    }

    /**
     * Duplicates a [collection](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection). An existing collection ID and new title are required. ## Publication Duplication Publications may be
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionDuplicateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionDuplicate(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionDuplicate', $args, ['input' => 'CollectionDuplicateInput!'], $selection);
    }

    /**
     * Publishes a collection to a channel.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionPublishInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionPublish(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } collectionPublications { isPublished publishDate } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionPublish', $args, ['input' => 'CollectionPublishInput!'], $selection);
    }

    /**
     * Removes multiple manually included products from a collection in a single operation. This mutation can process large product sets (up to 250 products) and may take significant time to complete for col
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, productIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionRemoveProducts(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionRemoveProducts', $args, ['id' => 'ID!', 'productIds' => '[ID!]!'], $selection);
    }

    /**
     * Asynchronously reorders products within a specified collection. Instead of returning an updated collection, this mutation returns a job, which should be [polled](https://shopify.dev/api/admin-graphql/
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, moves: [MoveInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionReorderProducts(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionReorderProducts', $args, ['id' => 'ID!', 'moves' => '[MoveInput!]!'], $selection);
    }

    /**
     * Unpublishes a collection.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: CollectionUnpublishInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionUnpublish(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionUnpublish', $args, ['input' => 'CollectionUnpublishInput!'], $selection);
    }

    /**
     * Updates a [collection](https://shopify.dev/docs/api/admin-graphql/latest/objects/Collection), modifying its properties, products, or publication settings. Collections help organize [products](https://
     *
     * @param array<string,mixed> $args Variaveis GraphQL: collection: CollectionUpdateInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function collectionUpdate(array $args = [], string $selection = 'collection { description descriptionHtml handle hasProduct id legacyResourceId publicationCount publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication sortOrder storefrontId templateSuffix title updatedAt } job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'collectionUpdate', $args, ['collection' => 'CollectionUpdateInput'], $selection);
    }
}
