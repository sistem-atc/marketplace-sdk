<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Delivery.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class DeliveryMutations extends BaseOperations
{
    /**
     * Creates a carrier service that provides real-time shipping rates to Shopify. Carrier services provide real-time shipping rates from external providers like FedEx, UPS, or custom shipping solutions. Th
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DeliveryCarrierServiceCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function carrierServiceCreate(array $args = [], string $selection = 'carrierService { active callbackUrl formattedName id name supportsServiceDiscovery } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'carrierServiceCreate', $args, ['input' => 'DeliveryCarrierServiceCreateInput!'], $selection);
    }

    /**
     * Removes an existing carrier service.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function carrierServiceDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'carrierServiceDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a carrier service. Only the app that creates a carrier service can update it.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: DeliveryCarrierServiceUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function carrierServiceUpdate(array $args = [], string $selection = 'carrierService { active callbackUrl formattedName id name supportsServiceDiscovery } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'carrierServiceUpdate', $args, ['input' => 'DeliveryCarrierServiceUpdateInput!'], $selection);
    }

    /**
     * Activates and deactivates delivery customizations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: ids: [ID!]!, enabled: Boolean!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomizationActivation(array $args = [], string $selection = 'ids userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryCustomizationActivation', $args, ['ids' => '[ID!]!', 'enabled' => 'Boolean!'], $selection);
    }

    /**
     * Creates a delivery customization.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: deliveryCustomization: DeliveryCustomizationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomizationCreate(array $args = [], string $selection = 'deliveryCustomization { enabled functionId id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryCustomizationCreate', $args, ['deliveryCustomization' => 'DeliveryCustomizationInput!'], $selection);
    }

    /**
     * Creates a delivery customization.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomizationDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryCustomizationDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a delivery customization.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, deliveryCustomization: DeliveryCustomizationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryCustomizationUpdate(array $args = [], string $selection = 'deliveryCustomization { enabled functionId id title } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryCustomizationUpdate', $args, ['id' => 'ID!', 'deliveryCustomization' => 'DeliveryCustomizationInput!'], $selection);
    }

    /**
     * Creates a [`DeliveryProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DeliveryProfile) that defines shipping rates for specific products and locations. A delivery profile groups pro
     *
     * @param array<string,mixed> $args Variaveis GraphQL: profile: DeliveryProfileInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryProfileCreate(array $args = [], string $selection = 'profile { activeMethodDefinitionsCount coversAllItems default id locationsWithoutRatesCount name originLocationCount version zoneCountryCount } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryProfileCreate', $args, ['profile' => 'DeliveryProfileInput!'], $selection);
    }

    /**
     * Enqueue the removal of a delivery profile.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryProfileRemove(array $args = [], string $selection = 'job { done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryProfileRemove', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a [`DeliveryProfile`](https://shopify.dev/docs/api/admin-graphql/latest/objects/DeliveryProfile)'s configuration, including its shipping zones, rates, and associated products. Modify location
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, profile: DeliveryProfileInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryProfileUpdate(array $args = [], string $selection = 'profile { activeMethodDefinitionsCount coversAllItems default id locationsWithoutRatesCount name originLocationCount version zoneCountryCount } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryProfileUpdate', $args, ['id' => 'ID!', 'profile' => 'DeliveryProfileInput!'], $selection);
    }

    /**
     * Updates the delivery promise participants by adding or removing owners based on a branded promise handle.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: brandedPromiseHandle: String!, ownersToAdd: [ID!], ownersToRemove: [ID!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryPromiseParticipantsUpdate(array $args = [], string $selection = 'promiseParticipants { id ownerType } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryPromiseParticipantsUpdate', $args, ['brandedPromiseHandle' => 'String!', 'ownersToAdd' => '[ID!]', 'ownersToRemove' => '[ID!]'], $selection);
    }

    /**
     * Creates or updates a delivery promise provider. Currently restricted to select approved delivery promise partners.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: active: Boolean, fulfillmentDelay: Int, timeZone: String, locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryPromiseProviderUpsert(array $args = [], string $selection = 'deliveryPromiseProvider { active fulfillmentDelay id timeZone } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryPromiseProviderUpsert', $args, ['active' => 'Boolean', 'fulfillmentDelay' => 'Int', 'timeZone' => 'String', 'locationId' => 'ID!'], $selection);
    }

    /**
     * Set the delivery settings for a shop.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: (sem argumentos)
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliverySettingUpdate(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliverySettingUpdate', $args, [], $selection);
    }

    /**
     * Assigns a location as the shipping origin while using legacy compatibility mode for multi-location delivery profiles. Deprecated as of 2026-04 and will be removed in a future version as single origin
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function deliveryShippingOriginAssign(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'deliveryShippingOriginAssign', $args, ['locationId' => 'ID!'], $selection);
    }

    /**
     * Purchases a shipping label for a fulfillment order. The `shippingLabelPurchase` mutation buys one shipping label for one fulfillment order using Shopify Shipping. The mutation validates the fulfillme
     *
     * @param array<string,mixed> $args Variaveis GraphQL: shippingLabelPurchase: ShippingLabelPurchaseInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shippingLabelPurchase(array $args = [], string $selection = 'shippingLabelPurchaseResult { done id status } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shippingLabelPurchase', $args, ['shippingLabelPurchase' => 'ShippingLabelPurchaseInput!'], $selection);
    }

    /**
     * Deletes a shipping package.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shippingPackageDelete(array $args = [], string $selection = 'deletedId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shippingPackageDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Set a shipping package as the default. The default shipping package is the one used to calculate shipping costs on checkout.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shippingPackageMakeDefault(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shippingPackageMakeDefault', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates a shipping package.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, shippingPackage: CustomShippingPackageInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function shippingPackageUpdate(array $args = [], string $selection = 'userErrors { field message }'): array
    {
        return $this->execute('mutation', 'shippingPackageUpdate', $args, ['id' => 'ID!', 'shippingPackage' => 'CustomShippingPackageInput!'], $selection);
    }
}
