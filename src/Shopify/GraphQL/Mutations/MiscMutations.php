<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Misc.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class MiscMutations extends BaseOperations
{
    /**
     * Creates product feedback for multiple products.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: feedbackInput: [ProductResourceFeedbackInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function bulkProductResourceFeedbackCreate(array $args = [], string $selection = 'feedback { feedbackGeneratedAt messages productId productUpdatedAt state } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'bulkProductResourceFeedbackCreate', $args, ['feedbackInput' => '[ProductResourceFeedbackInput!]!'], $selection);
    }

    /**
     * Creates a [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) representing a connection between the shop and an external selling platform account. Use this mutation after a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ChannelCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channelCreate(array $args = [], string $selection = 'channel { accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'channelCreate', $args, ['input' => 'ChannelCreateInput!'], $selection);
    }

    /**
     * Deletes a [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel) from the shop. All associated product feeds are removed. Existing orders attributed to the channel are preserve
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channelDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'channelDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Triggers a full product resync for the specified [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel). All published products are added to the channel's product feed attentio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: channelId: ID!, language: LanguageCode, country: CountryCode, beforeUpdatedAt: DateTime, updatedAtSince: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channelFullSync(array $args = [], string $selection = 'fullSyncTraceInfo { country language operationId } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'channelFullSync', $args, ['channelId' => 'ID!', 'language' => 'LanguageCode', 'country' => 'CountryCode', 'beforeUpdatedAt' => 'DateTime', 'updatedAtSince' => 'DateTime'], $selection);
    }

    /**
     * Updates the properties of an existing [`Channel`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Channel). Use this mutation to update account information — such as the display name shown i
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: ChannelUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function channelUpdate(array $args = [], string $selection = 'channel { accountId accountName activeRegions handle hasCollection id name overviewPath specificationHandle supportsFuturePublishing } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'channelUpdate', $args, ['id' => 'ID!', 'input' => 'ChannelUpdateInput!'], $selection);
    }

    /**
     * Add, remove and update `CombinedListing`s of a given Product. `CombinedListing`s are comprised of multiple products to create a single listing. There are two kinds of products used in a `CombinedList
     *
     * @param array<string,mixed> $args Variaveis GraphQL: parentProductId: ID!, title: String, productsAdded: [ChildProductRelationInput!], productsEdited: [ChildProductRelationInput!], productsRemovedIds: [ID!], optionsAndValues: [OptionAndValueInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function combinedListingUpdate(array $args = [], string $selection = 'product { bodyHtml combinedListingRole createdAt customProductType defaultCursor description descriptionHtml descriptionPlainSummary giftCardTemplateSuffix handle hasOnlyDefaultVariant hasOutOfStockVariants hasVariantsThatRequiresComponents id inCollection isGiftCard legacyResourceId onlineStorePreviewUrl onlineStoreUrl productType publicationCount publishedAt publishedInContext publishedOnChannel publishedOnCurrentChannel publishedOnCurrentPublication publishedOnPublication requiresSellingPlan sellingPlanGroupCount status storefrontId tags templateSuffix title totalInventory totalVariants tracksInventory updatedAt vendor } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'combinedListingUpdate', $args, ['parentProductId' => 'ID!', 'title' => 'String', 'productsAdded' => '[ChildProductRelationInput!]', 'productsEdited' => '[ChildProductRelationInput!]', 'productsRemovedIds' => '[ID!]', 'optionsAndValues' => '[OptionAndValueInput!]'], $selection);
    }

    /**
     * Update or create consent policies in bulk.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: consentPolicies: [ConsentPolicyInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function consentPolicyUpdate(array $args = [], string $selection = 'updatedPolicies { consentRequired countryCode dataSaleOptOutRequired id regionCode shopId } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'consentPolicyUpdate', $args, ['consentPolicies' => '[ConsentPolicyInput!]!'], $selection);
    }

    /**
     * Opt out a customer from data sale.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: email: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function dataSaleOptOut(array $args = [], string $selection = 'customerId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'dataSaleOptOut', $args, ['email' => 'String!'], $selection);
    }

    /**
     * Creates a [`DelegateAccessToken`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DelegateAccessToken) with a subset of the parent token's permissions. Delegate access tokens enable secure
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DelegateAccessTokenInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function delegateAccessTokenCreate(array $args = [], string $selection = 'delegateAccessToken { accessScopes accessToken createdAt expiresIn } shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'delegateAccessTokenCreate', $args, ['input' => 'DelegateAccessTokenInput!'], $selection);
    }

    /**
     * Destroys a delegate access token.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: accessToken: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function delegateAccessTokenDestroy(array $args = [], string $selection = 'shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } status userErrors { field message }'): array
    {
        return $this->execute('mutation', 'delegateAccessTokenDestroy', $args, ['accessToken' => 'String!'], $selection);
    }

    /**
     * Generates a signature for a Flow action payload.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, payload: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function flowGenerateSignature(array $args = [], string $selection = 'payload signature targetUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'flowGenerateSignature', $args, ['id' => 'ID!', 'payload' => 'String!'], $selection);
    }

    /**
     * Triggers any workflows that begin with the trigger specified in the request body. To learn more, refer to [_Create Shopify Flow triggers_](https://shopify.dev/apps/flow/triggers).
     *
     * @param array<string,mixed> $args Variaveis GraphQL: handle: String, payload: JSON
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function flowTriggerReceive(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'flowTriggerReceive', $args, ['handle' => 'String', 'payload' => 'JSON'], $selection);
    }

    /**
     * Create a mobile platform application.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: MobilePlatformApplicationCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function mobilePlatformApplicationCreate(array $args = [], string $selection = 'mobilePlatformApplication { __typename } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'mobilePlatformApplicationCreate', $args, ['input' => 'MobilePlatformApplicationCreateInput!'], $selection);
    }

    /**
     * Delete a mobile platform application.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function mobilePlatformApplicationDelete(array $args = [], string $selection = 'deletedMobilePlatformApplicationId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'mobilePlatformApplicationDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Update a mobile platform application.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: MobilePlatformApplicationUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function mobilePlatformApplicationUpdate(array $args = [], string $selection = 'mobilePlatformApplication { __typename } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'mobilePlatformApplicationUpdate', $args, ['id' => 'ID!', 'input' => 'MobilePlatformApplicationUpdateInput!'], $selection);
    }

    /**
     * Installs storefront generation preview for shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: storefrontGenerationPreviewUuid: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function previewInstall(array $args = [], string $selection = 'newTheme { createdAt id name prefix processing processingFailed role themeStoreId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'previewInstall', $args, ['storefrontGenerationPreviewUuid' => 'String!'], $selection);
    }

    /**
     * Disable a shop's privacy features.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: featuresToDisable: [PrivacyFeaturesEnum!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function privacyFeaturesDisable(array $args = [], string $selection = 'featuresDisabled userErrors { field message }'): array
    {
        return $this->execute('mutation', 'privacyFeaturesDisable', $args, ['featuresToDisable' => '[PrivacyFeaturesEnum!]!'], $selection);
    }

    /**
     * Updates the server pixel to connect to a Google PubSub endpoint. Running this mutation deletes any previous subscriptions for the server pixel.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: pubSubProject: String!, pubSubTopic: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function pubSubServerPixelUpdate(array $args = [], string $selection = 'serverPixel { id status webhookEndpointAddress } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'pubSubServerPixelUpdate', $args, ['pubSubProject' => 'String!', 'pubSubTopic' => 'String!'], $selection);
    }

    /**
     * Creates a saved search.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: SavedSearchCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function savedSearchCreate(array $args = [], string $selection = 'savedSearch { id legacyResourceId name query resourceType searchTerms } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'savedSearchCreate', $args, ['input' => 'SavedSearchCreateInput!'], $selection);
    }

    /**
     * Delete a saved search.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: SavedSearchDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function savedSearchDelete(array $args = [], string $selection = 'deletedSavedSearchId shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'savedSearchDelete', $args, ['input' => 'SavedSearchDeleteInput!'], $selection);
    }

    /**
     * Updates a saved search.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: SavedSearchUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function savedSearchUpdate(array $args = [], string $selection = 'savedSearch { id legacyResourceId name query resourceType searchTerms } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'savedSearchUpdate', $args, ['input' => 'SavedSearchUpdateInput!'], $selection);
    }

    /**
     * <div class="note"><h4>Theme app extensions</h4> <p>If your app integrates with a Shopify theme and you plan to submit it to the Shopify App Store, you must use theme app extensions instead of Script
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ScriptTagInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function scriptTagCreate(array $args = [], string $selection = 'scriptTag { cache createdAt displayScope id legacyResourceId src updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'scriptTagCreate', $args, ['input' => 'ScriptTagInput!'], $selection);
    }

    /**
     * <div class="note"><h4>Theme app extensions</h4> <p>If your app integrates with a Shopify theme and you plan to submit it to the Shopify App Store, you must use theme app extensions instead of Script
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function scriptTagDelete(array $args = [], string $selection = 'deletedScriptTagId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'scriptTagDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * <div class="note"><h4>Theme app extensions</h4> <p>If your app integrates with a Shopify theme and you plan to submit it to the Shopify App Store, you must use theme app extensions instead of Script
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, input: ScriptTagInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function scriptTagUpdate(array $args = [], string $selection = 'scriptTag { cache createdAt displayScope id legacyResourceId src updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'scriptTagUpdate', $args, ['id' => 'ID!', 'input' => 'ScriptTagInput!'], $selection);
    }

    /**
     * Creates a new unconfigured server pixel. A single server pixel can exist for an app and shop combination. If you call this mutation when a server pixel already exists, then an error will return.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function serverPixelCreate(array $args = [], string $selection = 'serverPixel { id status webhookEndpointAddress } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'serverPixelCreate', $args, [], $selection);
    }

    /**
     * Deletes the Server Pixel associated with the current app & shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function serverPixelDelete(array $args = [], string $selection = 'deletedServerPixelId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'serverPixelDelete', $args, [], $selection);
    }

    /**
     * Adds tags to a resource. If the resource type doesn't support tagging, the `id` argument returns a resource-not-found error. Tags help merchants organize and filter resources. See the [`tags`](https:
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function tagsAdd(array $args = [], string $selection = 'node { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'tagsAdd', $args, ['id' => 'ID!', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Removes tags from a resource. If the resource type doesn't support tagging, the `id` argument returns a resource-not-found error. Tags are searchable keywords that help organize and filter these reso
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, tags: [String!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function tagsRemove(array $args = [], string $selection = 'node { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'tagsRemove', $args, ['id' => 'ID!', 'tags' => '[String!]!'], $selection);
    }

    /**
     * Allows tax app configurations for tax partners.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ready: Boolean!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function taxAppConfigure(array $args = [], string $selection = 'taxAppConfiguration { state } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'taxAppConfigure', $args, ['ready' => 'Boolean!'], $selection);
    }

    /**
     * Creates a tax summary for a given order. If both an order ID and a start and end time are provided, the order ID will be used.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: orderId: ID, startTime: DateTime, endTime: DateTime
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function taxSummaryCreate(array $args = [], string $selection = 'enqueuedOrders { billingAddressMatchesShippingAddress canMarkAsPaid canNotifyCustomer cancelReason cancelledAt capturable cartDiscountAmount cartToken checkoutToken clientIp closed closedAt confirmationNumber confirmed createdAt currencyCode currentSubtotalLineItemsQuantity currentTotalWeight customerAcceptsMarketing customerLocale discountCode discountCodes displayFinancialStatus displayFulfillmentStatus dutiesIncluded edited email estimatedTaxes fulfillable fullyPaid hasTimelineComment id landingPageDisplayText landingPageUrl legacyResourceId merchantEditable merchantEditableErrors name netPayment note number paymentGatewayNames phone poNumber presentmentCurrencyCode processedAt productNetwork referralCode referrerDisplayText referrerUrl refundable registeredSourceUrl requiresShipping restockable returnStatus riskLevel sourceIdentifier sourceName statusPageUrl subtotalLineItemsQuantity subtotalPrice tags taxExempt taxesIncluded test totalCapturable totalDiscounts totalPrice totalReceived totalRefunded totalShippingPrice totalTax totalWeight unpaid updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'taxSummaryCreate', $args, ['orderId' => 'ID', 'startTime' => 'DateTime', 'endTime' => 'DateTime'], $selection);
    }
}
