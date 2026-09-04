<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Company.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class CompanyMutations extends BaseOperations
{
    /**
     * Deletes a list of companies.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companiesDelete(array $args = [], string $selection = 'deletedCompanyIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companiesDelete', $args, ['companyIds' => '[ID!]!'], $selection);
    }

    /**
     * Deletes a company address.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: addressId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyAddressDelete(array $args = [], string $selection = 'deletedAddressId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyAddressDelete', $args, ['addressId' => 'ID!'], $selection);
    }

    /**
     * Adds an existing [`Customer`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Customer) as a contact to a [`Company`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company). Com
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!, customerId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyAssignCustomerAsContact(array $args = [], string $selection = 'companyContact { createdAt id isMainContact lifetimeDuration locale title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyAssignCustomerAsContact', $args, ['companyId' => 'ID!', 'customerId' => 'ID!'], $selection);
    }

    /**
     * Assigns the main contact for the company.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!, companyContactId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyAssignMainContact(array $args = [], string $selection = 'company { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyAssignMainContact', $args, ['companyId' => 'ID!', 'companyContactId' => 'ID!'], $selection);
    }

    /**
     * Assigns a role to a contact for a location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, companyContactRoleId: ID!, companyLocationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactAssignRole(array $args = [], string $selection = 'companyContactRoleAssignment { createdAt id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactAssignRole', $args, ['companyContactId' => 'ID!', 'companyContactRoleId' => 'ID!', 'companyLocationId' => 'ID!'], $selection);
    }

    /**
     * Assigns roles on a company contact.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, rolesToAssign: [CompanyContactRoleAssign!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactAssignRoles(array $args = [], string $selection = 'roleAssignments { createdAt id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactAssignRoles', $args, ['companyContactId' => 'ID!', 'rolesToAssign' => '[CompanyContactRoleAssign!]!'], $selection);
    }

    /**
     * Creates a company contact and the associated customer.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!, input: CompanyContactInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactCreate(array $args = [], string $selection = 'companyContact { createdAt id isMainContact lifetimeDuration locale title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactCreate', $args, ['companyId' => 'ID!', 'input' => 'CompanyContactInput!'], $selection);
    }

    /**
     * Deletes a company contact.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactDelete(array $args = [], string $selection = 'deletedCompanyContactId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactDelete', $args, ['companyContactId' => 'ID!'], $selection);
    }

    /**
     * Removes a company contact from a Company.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactRemoveFromCompany(array $args = [], string $selection = 'removedCompanyContactId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactRemoveFromCompany', $args, ['companyContactId' => 'ID!'], $selection);
    }

    /**
     * Revokes a role on a company contact.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, companyContactRoleAssignmentId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactRevokeRole(array $args = [], string $selection = 'revokedCompanyContactRoleAssignmentId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactRevokeRole', $args, ['companyContactId' => 'ID!', 'companyContactRoleAssignmentId' => 'ID!'], $selection);
    }

    /**
     * Revokes roles on a company contact.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, roleAssignmentIds: [ID!], revokeAll: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactRevokeRoles(array $args = [], string $selection = 'revokedRoleAssignmentIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactRevokeRoles', $args, ['companyContactId' => 'ID!', 'roleAssignmentIds' => '[ID!]', 'revokeAll' => 'Boolean'], $selection);
    }

    /**
     * Sends the company contact a welcome email.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, email: EmailInput
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactSendWelcomeEmail(array $args = [], string $selection = 'companyContact { createdAt id isMainContact lifetimeDuration locale title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactSendWelcomeEmail', $args, ['companyContactId' => 'ID!', 'email' => 'EmailInput'], $selection);
    }

    /**
     * Updates a company contact.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactId: ID!, input: CompanyContactInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactUpdate(array $args = [], string $selection = 'companyContact { createdAt id isMainContact lifetimeDuration locale title updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactUpdate', $args, ['companyContactId' => 'ID!', 'input' => 'CompanyContactInput!'], $selection);
    }

    /**
     * Deletes one or more company contacts.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyContactIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyContactsDelete(array $args = [], string $selection = 'deletedCompanyContactIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyContactsDelete', $args, ['companyContactIds' => '[ID!]!'], $selection);
    }

    /**
     * Creates a [`Company`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company) for B2B commerce. This mutation creates the company and can optionally create an initial [`CompanyContact`](htt
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: CompanyCreateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyCreate(array $args = [], string $selection = 'company { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyCreate', $args, ['input' => 'CompanyCreateInput!'], $selection);
    }

    /**
     * Deletes a company.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyDelete(array $args = [], string $selection = 'deletedCompanyId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyDelete', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Updates an address on a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, address: CompanyAddressInput!, addressTypes: [CompanyAddressType!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationAssignAddress(array $args = [], string $selection = 'addresses { address1 address2 city companyName country countryCode createdAt firstName formattedAddress formattedArea id lastName phone province recipient updatedAt zip zoneCode } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationAssignAddress', $args, ['locationId' => 'ID!', 'address' => 'CompanyAddressInput!', 'addressTypes' => '[CompanyAddressType!]!'], $selection);
    }

    /**
     * Assigns roles on a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, rolesToAssign: [CompanyLocationRoleAssign!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationAssignRoles(array $args = [], string $selection = 'roleAssignments { createdAt id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationAssignRoles', $args, ['companyLocationId' => 'ID!', 'rolesToAssign' => '[CompanyLocationRoleAssign!]!'], $selection);
    }

    /**
     * Creates one or more mappings between a staff member at a shop and a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, staffMemberIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationAssignStaffMembers(array $args = [], string $selection = 'companyLocationStaffMemberAssignments { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationAssignStaffMembers', $args, ['companyLocationId' => 'ID!', 'staffMemberIds' => '[ID!]!'], $selection);
    }

    /**
     * Assigns tax exemptions to the company location.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, taxExemptions: [TaxExemption!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationAssignTaxExemptions(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationAssignTaxExemptions', $args, ['companyLocationId' => 'ID!', 'taxExemptions' => '[TaxExemption!]!'], $selection);
    }

    /**
     * Creates a new location for a [`Company`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company). Company locations are branches or offices where B2B customers can place orders with specifi
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!, input: CompanyLocationInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationCreate(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationCreate', $args, ['companyId' => 'ID!', 'input' => 'CompanyLocationInput!'], $selection);
    }

    /**
     * Creates a tax registration for a company location.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: locationId: ID!, taxId: String!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationCreateTaxRegistration(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationCreateTaxRegistration', $args, ['locationId' => 'ID!', 'taxId' => 'String!'], $selection);
    }

    /**
     * Deletes a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationDelete(array $args = [], string $selection = 'deletedCompanyLocationId userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationDelete', $args, ['companyLocationId' => 'ID!'], $selection);
    }

    /**
     * Deletes one or more existing mappings between a staff member at a shop and a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationStaffMemberAssignmentIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationRemoveStaffMembers(array $args = [], string $selection = 'deletedCompanyLocationStaffMemberAssignmentIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationRemoveStaffMembers', $args, ['companyLocationStaffMemberAssignmentIds' => '[ID!]!'], $selection);
    }

    /**
     * Revokes roles on a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, rolesToRevoke: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationRevokeRoles(array $args = [], string $selection = 'revokedRoleAssignmentIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationRevokeRoles', $args, ['companyLocationId' => 'ID!', 'rolesToRevoke' => '[ID!]!'], $selection);
    }

    /**
     * Revokes tax exemptions from the company location.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, taxExemptions: [TaxExemption!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationRevokeTaxExemptions(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationRevokeTaxExemptions', $args, ['companyLocationId' => 'ID!', 'taxExemptions' => '[TaxExemption!]!'], $selection);
    }

    /**
     * Revokes tax registration on a company location.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationRevokeTaxRegistration(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationRevokeTaxRegistration', $args, ['companyLocationId' => 'ID!'], $selection);
    }

    /**
     * Sets the tax settings for a company location.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, taxRegistrationId: String, taxExempt: Boolean, exemptionsToAssign: [TaxExemption!], exemptionsToRemove: [TaxExemption!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationTaxSettingsUpdate(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationTaxSettingsUpdate', $args, ['companyLocationId' => 'ID!', 'taxRegistrationId' => 'String', 'taxExempt' => 'Boolean', 'exemptionsToAssign' => '[TaxExemption!]', 'exemptionsToRemove' => '[TaxExemption!]'], $selection);
    }

    /**
     * Updates a company location's information and B2B checkout settings. Company locations are branches or offices where [`CompanyContact`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationId: ID!, input: CompanyLocationUpdateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationUpdate(array $args = [], string $selection = 'companyLocation { createdAt currency defaultCursor externalId hasTimelineComment id inCatalog locale name note orderCount phone taxExemptions taxRegistrationId updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationUpdate', $args, ['companyLocationId' => 'ID!', 'input' => 'CompanyLocationUpdateInput!'], $selection);
    }

    /**
     * Deletes a list of company locations.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyLocationIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyLocationsDelete(array $args = [], string $selection = 'deletedCompanyLocationIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyLocationsDelete', $args, ['companyLocationIds' => '[ID!]!'], $selection);
    }

    /**
     * Revokes the main contact from the company.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyRevokeMainContact(array $args = [], string $selection = 'company { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyRevokeMainContact', $args, ['companyId' => 'ID!'], $selection);
    }

    /**
     * Updates a [`Company`](https://shopify.dev/docs/api/admin-graphql/latest/objects/Company) with new information. Companies represent business customers that can have multiple contacts and locations with
     *
     * @param array<string,mixed> $args Variaveis GraphQL: companyId: ID!, input: CompanyInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function companyUpdate(array $args = [], string $selection = 'company { contactCount createdAt customerSince defaultCursor externalId hasTimelineComment id lifetimeDuration name note updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'companyUpdate', $args, ['companyId' => 'ID!', 'input' => 'CompanyInput!'], $selection);
    }
}
