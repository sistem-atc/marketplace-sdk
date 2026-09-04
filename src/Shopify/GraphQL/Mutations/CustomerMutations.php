<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Customer.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CustomerMutations extends BaseOperations
{
    /**
     * Add tax exemptions for the customer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, taxExemptions: [TaxExemption!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAddTaxExemptions(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerAddTaxExemptions', $args, ['customerId' => 'ID!', 'taxExemptions' => '[TaxExemption!]!'], $selection);
    }

    /**
     * Creates a new [`MailingAddress`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MailingAddress) for a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer). You c
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, address: MailingAddressInput!, setAsDefault: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAddressCreate(array $args = [], string $selection = 'address { address1 address2 city company coordinatesValidated country countryCode countryCodeV2 firstName formatted formattedArea id lastName latitude longitude name phone province provinceCode timeZone validationResultSummary zip } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerAddressCreate', $args, ['customerId' => 'ID!', 'address' => 'MailingAddressInput!', 'setAsDefault' => 'Boolean'], $selection);
    }

    /**
     * Deletes a customer's address.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, addressId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAddressDelete(array $args = [], string $selection = 'deletedAddressId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerAddressDelete', $args, ['customerId' => 'ID!', 'addressId' => 'ID!'], $selection);
    }

    /**
     * Updates a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer)'s [`MailingAddress`](https://shopify.dev/docs/api/admin-graphql/latest/objects/MailingAddress). You can modif
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, addressId: ID!, address: MailingAddressInput!, setAsDefault: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerAddressUpdate(array $args = [], string $selection = 'address { address1 address2 city company coordinatesValidated country countryCode countryCodeV2 firstName formatted formattedArea id lastName latitude longitude name phone province provinceCode timeZone validationResultSummary zip } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerAddressUpdate', $args, ['customerId' => 'ID!', 'addressId' => 'ID!', 'address' => 'MailingAddressInput!', 'setAsDefault' => 'Boolean'], $selection);
    }

    /**
     * Cancels a pending erasure of a customer's data. Read more [here](https://help.shopify.com/manual/privacy-and-security/privacy/processing-customer-data-requests#cancel-customer-data-erasure). To reque
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerCancelDataErasure(array $args = [], string $selection = 'customerId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerCancelDataErasure', $args, ['customerId' => 'ID!'], $selection);
    }

    /**
     * Creates a new [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer) in the store. Accepts customer details including contact information, marketing consent preferences, and
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerCreate(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerCreate', $args, ['input' => 'CustomerInput!'], $selection);
    }

    /**
     * Deletes a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer) from the store. You can only delete customers who haven't placed any [orders](https://shopify.dev/docs/api/ad
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerDeleteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerDelete(array $args = [], string $selection = 'deletedCustomerId shop { analyticsToken checkoutApiSupported contactEmail createdAt currencyCode customerAccounts description email enabledPresentmentCurrencies ianaTimezone id marketingSmsConsentEnabledAtCheckout myshopifyDomain name orderNumberFormatPrefix orderNumberFormatSuffix publicationCount richTextEditorUrl setupRequired shipsToCountries shopOwnerName storefrontUrl taxShipping taxesIncluded timezoneAbbreviation timezoneOffset timezoneOffsetMinutes transactionalSmsDisabled unitSystem updatedAt url weightUnit } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerDelete', $args, ['input' => 'CustomerDeleteInput!'], $selection);
    }

    /**
     * Updates a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer)'s email marketing consent information. The customer must have an email address to update their consent. Recor
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerEmailMarketingConsentUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerEmailMarketingConsentUpdate(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerEmailMarketingConsentUpdate', $args, ['input' => 'CustomerEmailMarketingConsentUpdateInput!'], $selection);
    }

    /**
     * Generates a one-time activation URL for a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer) whose legacy customer account isn't yet enabled. Use this after importing cus
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerGenerateAccountActivationUrl(array $args = [], string $selection = 'accountActivationUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerGenerateAccountActivationUrl', $args, ['customerId' => 'ID!'], $selection);
    }

    /**
     * Merges two customers. The `customerOneId` and `customerTwoId` arguments don't guarantee which customer is kept. Shopify selects the resulting customer in this order: 1. If `overrideFields.customerIdO
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerOneId: ID!, customerTwoId: ID!, overrideFields: CustomerMergeOverrideFields
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerMerge(array $args = [], string $selection = 'job { done id } resultingCustomerId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerMerge', $args, ['customerOneId' => 'ID!', 'customerTwoId' => 'ID!', 'overrideFields' => 'CustomerMergeOverrideFields'], $selection);
    }

    /**
     * Creates a vaulted payment method for a customer from duplication data. This data must be obtained from another shop within the same organization. Currently, this only supports Shop Pay payment metho
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, billingAddress: MailingAddressInput!, encryptedDuplicationData: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodCreateFromDuplicationData(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodCreateFromDuplicationData', $args, ['customerId' => 'ID!', 'billingAddress' => 'MailingAddressInput!', 'encryptedDuplicationData' => 'String!'], $selection);
    }

    /**
     * Creates a credit card payment method for a customer using a session id. These values are only obtained through card imports happening from a PCI compliant environment. Please use customerPaymentMethod
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, billingAddress: MailingAddressInput!, sessionId: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodCreditCardCreate(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } processing userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodCreditCardCreate', $args, ['customerId' => 'ID!', 'billingAddress' => 'MailingAddressInput!', 'sessionId' => 'String!'], $selection);
    }

    /**
     * Updates an existing vaulted credit card payment method for a customer, including billing address and card details. Requires a valid cardserver session from a PCI-compliant environment. Use this when a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, billingAddress: MailingAddressInput!, sessionId: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodCreditCardUpdate(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } processing userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodCreditCardUpdate', $args, ['id' => 'ID!', 'billingAddress' => 'MailingAddressInput!', 'sessionId' => 'String!'], $selection);
    }

    /**
     * Returns encrypted data that can be used to duplicate the payment method in another shop within the same organization. Currently, this only supports Shop Pay payment methods. This is only available fo
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerPaymentMethodId: ID!, targetShopId: ID!, targetCustomerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodGetDuplicationData(array $args = [], string $selection = 'encryptedDuplicationData userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodGetDuplicationData', $args, ['customerPaymentMethodId' => 'ID!', 'targetShopId' => 'ID!', 'targetCustomerId' => 'ID!'], $selection);
    }

    /**
     * Returns a URL that allows the customer to update a specific payment method. Currently, `customerPaymentMethodGetUpdateUrl` only supports Shop Pay.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerPaymentMethodId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodGetUpdateUrl(array $args = [], string $selection = 'updatePaymentMethodUrl userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodGetUpdateUrl', $args, ['customerPaymentMethodId' => 'ID!'], $selection);
    }

    /**
     * Creates a vaulted PayPal billing agreement for a customer, enabling recurring charges through PayPal. The billing agreement ID (starting with 'B-') must be obtained from PayPal. Once created, this pay
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, billingAddress: MailingAddressInput, billingAgreementId: String!, inactive: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodPaypalBillingAgreementCreate(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodPaypalBillingAgreementCreate', $args, ['customerId' => 'ID!', 'billingAddress' => 'MailingAddressInput', 'billingAgreementId' => 'String!', 'inactive' => 'Boolean'], $selection);
    }

    /**
     * Updates the billing address associated with a customer's vaulted PayPal billing agreement. Use this when a customer's billing information has changed and their PayPal payment method record in Shopify
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!, billingAddress: MailingAddressInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodPaypalBillingAgreementUpdate(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodPaypalBillingAgreementUpdate', $args, ['id' => 'ID!', 'billingAddress' => 'MailingAddressInput!'], $selection);
    }

    /**
     * Creates a customer payment method using identifiers from remote payment gateways like Stripe, Authorize.Net, or Braintree. Imports existing payment methods from external gateways and associates them w
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, remoteReference: CustomerPaymentMethodRemoteInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodRemoteCreate(array $args = [], string $selection = 'customerPaymentMethod { id revokedAt revokedReason } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodRemoteCreate', $args, ['customerId' => 'ID!', 'remoteReference' => 'CustomerPaymentMethodRemoteInput!'], $selection);
    }

    /**
     * Revokes a customer's vaulted payment method, preventing it from being used for future charges such as subscriptions, draft orders, or other payments. Revocation will fail if the payment method has act
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerPaymentMethodId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodRevoke(array $args = [], string $selection = 'revokedCustomerPaymentMethodId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodRevoke', $args, ['customerPaymentMethodId' => 'ID!'], $selection);
    }

    /**
     * Sends an email to a customer containing a secure link to update a specific vaulted payment method. This is commonly used when a customer's credit card is expiring or has been declined, and they need t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerPaymentMethodId: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerPaymentMethodSendUpdateEmail(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerPaymentMethodSendUpdateEmail', $args, ['customerPaymentMethodId' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Remove tax exemptions from a customer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, taxExemptions: [TaxExemption!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerRemoveTaxExemptions(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerRemoveTaxExemptions', $args, ['customerId' => 'ID!', 'taxExemptions' => '[TaxExemption!]!'], $selection);
    }

    /**
     * Replace tax exemptions for a customer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, taxExemptions: [TaxExemption!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerReplaceTaxExemptions(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerReplaceTaxExemptions', $args, ['customerId' => 'ID!', 'taxExemptions' => '[TaxExemption!]!'], $selection);
    }

    /**
     * Enqueues a request to erase customer's data. Read more [here](https://help.shopify.com/manual/privacy-and-security/privacy/processing-customer-data-requests#erase-customer-personal-data). To cancel t
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerRequestDataErasure(array $args = [], string $selection = 'customerId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerRequestDataErasure', $args, ['customerId' => 'ID!'], $selection);
    }

    /**
     * Creates a customer segment members query.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerSegmentMembersQueryInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSegmentMembersQueryCreate(array $args = [], string $selection = 'customerSegmentMembersQuery { currentCount done id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerSegmentMembersQueryCreate', $args, ['input' => 'CustomerSegmentMembersQueryInput!'], $selection);
    }

    /**
     * Sends an email invitation for a customer to create a legacy customer account. The invitation lets customers set up their password and activate their account in the online store. You can optionally cu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSendAccountInviteEmail(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerSendAccountInviteEmail', $args, ['customerId' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Creates or updates a customer in a single mutation. Use this mutation when syncing information from an external data source into Shopify. This mutation can be used to create a new customer, update a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerSetInput!, identifier: CustomerSetIdentifiers
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSet(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerSet', $args, ['input' => 'CustomerSetInput!', 'identifier' => 'CustomerSetIdentifiers'], $selection);
    }

    /**
     * Updates a [customer](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer)'s SMS marketing consent information. The customer must have a phone number on their account to receive SMS mark
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerSmsMarketingConsentUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerSmsMarketingConsentUpdate(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerSmsMarketingConsentUpdate', $args, ['input' => 'CustomerSmsMarketingConsentUpdateInput!'], $selection);
    }

    /**
     * Updates a [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer)'s attributes including personal information and [`tax exemptions`](https://shopify.dev/docs/api/admin-graphql
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerUpdate(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerUpdate', $args, ['input' => 'CustomerInput!'], $selection);
    }

    /**
     * Updates a customer's default address.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: customerId: ID!, addressId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerUpdateDefaultAddress(array $args = [], string $selection = 'customer { canDelete createdAt dataSaleOptOut displayName email firstName hasTimelineComment id lastName legacyResourceId lifetimeDuration locale multipassIdentifier note numberOfOrders phone productSubscriberStatus state tags taxExempt taxExemptions unsubscribeUrl updatedAt validEmailAddress verifiedEmail } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerUpdateDefaultAddress', $args, ['customerId' => 'ID!', 'addressId' => 'ID!'], $selection);
    }

    /**
     * Updates a [customer](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer)'s WhatsApp marketing consent information. Shopify identifies the customer's WhatsApp account by their [phone nu
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CustomerWhatsAppMarketingConsentUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function customerWhatsAppMarketingConsentUpdate(array $args = [], string $selection = 'customerPhoneNumber { marketingCollectedFrom marketingOptInLevel marketingState marketingUpdatedAt phoneNumber } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'customerWhatsAppMarketingConsentUpdate', $args, ['input' => 'CustomerWhatsAppMarketingConsentUpdateInput!'], $selection);
    }
}
