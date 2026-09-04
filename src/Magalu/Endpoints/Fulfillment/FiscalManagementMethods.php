<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Magalu Entregas / Fulfillment — GESTAO FISCAL do estoque no CD
 * (/logistics/fiscal-management/v1, base `services.magalu.com`).
 *
 * A Magalu emite a NF-e de saida do CD em nome do seller; pra isso precisa
 * dos "departamentos fiscais" (regras de ICMS/IPI/PIS/COFINS por destino e
 * tipo de cliente), do cadastro fiscal dos produtos (NCM/CEST/origem/EAN),
 * dos kits (composicao), dos parametros da empresa e das inscricoes
 * estaduais. Paginacao `_limit`/`_offset`; filtros `campo__in` (CSV) e
 * `campo__like`.
 */
class FiscalManagementMethods extends BaseMethods
{
    private const BASE = '/logistics/fiscal-management/v1';

    // ---------------------------------------------------------------- IE

    /**
     * Cadastra inscricoes estaduais (POST .../companies/state-inscriptions).
     *
     * @param list<array{state: string, inscription?: string, nfeComments?: string, gnre: bool}> $inscriptions
     * @return array<string, mixed>
     */
    public function createStateInscriptions(array $inscriptions): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl(self::BASE.'/companies/state-inscriptions'), [], ['inscriptions' => array_values($inscriptions)]);
    }

    /**
     * Atualiza inscricoes estaduais (PUT .../companies/state-inscriptions).
     *
     * @param list<array<string, mixed>> $inscriptions
     * @return array<string, mixed>
     */
    public function updateStateInscriptions(array $inscriptions): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl(self::BASE.'/companies/state-inscriptions'), [], ['inscriptions' => array_values($inscriptions)]);
    }

    // -------------------------------------------------------- parametros

    /**
     * Parametros fiscais da empresa (GET .../parameters/company). `typeIn` → `type__in`.
     *
     * @param list<int|string> $typeIn
     * @return array<string, mixed>
     */
    public function listCompanyParameters(array $typeIn = [], int $limit = 50, int $offset = 0): array
    {
        $query = ['_limit' => $limit, '_offset' => $offset];
        if ($typeIn) $query['type__in'] = implode(',', $typeIn);

        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE.'/parameters/company'), $query);
    }

    /**
     * Cria parametros (POST .../parameters/company) — 201.
     *
     * @param list<array{type: int, value: string}> $parameters
     * @return array<string, mixed>
     */
    public function createCompanyParameters(array $parameters): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl(self::BASE.'/parameters/company'), [], ['parameters' => array_values($parameters)]);
    }

    /**
     * Atualiza parametros (PUT .../parameters/company).
     *
     * @param list<array{type: int, value: string}> $parameters
     * @return array<string, mixed>
     */
    public function updateCompanyParameters(array $parameters): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl(self::BASE.'/parameters/company'), [], ['parameters' => array_values($parameters)]);
    }

    // ---------------------------------------------------------- produtos

    /**
     * Produtos e composicoes com dados fiscais (GET .../products).
     * Filtros: `sku__in`, `department.id__in`, `department.description__like`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listProducts(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE.'/products'), array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Produto por id/SKU (GET .../products/{id}).
     *
     * @return array<string, mixed>
     */
    public function getProduct(string $productId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE.'/products/'.rawurlencode($productId)));
    }

    /**
     * Atualiza dados fiscais do produto (PUT .../products/{id}).
     * Body: `department_id`, `ncm`, `cest`, `origin`, `ean`, `is_durable`, `warranty_time`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateProduct(string $productId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl(self::BASE.'/products/'.rawurlencode($productId)), [], $data);
    }

    /**
     * Atualiza varios produtos (PATCH .../products/batch). Body e' LISTA com `id` + campos.
     *
     * @param list<array<string, mixed>> $products
     * @return array<string, mixed>
     */
    public function updateProductsBatch(array $products): array
    {
        return $this->makeRequest(HttpMethod::PATCH, $this->servicesUrl(self::BASE.'/products/batch'), [], array_values($products));
    }

    // -------------------------------------------------------------- kits

    /**
     * Kits (GET .../kits). `sellerId` e' OBRIGATORIO (`seller.id`); filtros
     * `sku__in`, `department.id__in`, `department.description__like`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listKits(string $sellerId, array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE.'/kits'), array_merge($filters, [
            'seller.id' => $sellerId,
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Cria kit (POST .../kits/{id}) — `id` = SKU do kit; 201.
     *
     * @param list<array<string, mixed>> $compositions itens {sku, quantity, department_id, ncm, cest, origin, ...}
     * @return array<string, mixed>
     */
    public function createKit(string $kitSku, array $compositions): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl(self::BASE.'/kits/'.rawurlencode($kitSku)), [], ['compositions' => array_values($compositions)]);
    }

    /**
     * Substitui a composicao do kit (PUT .../kits/{id}).
     *
     * @param list<array<string, mixed>> $compositions
     * @return array<string, mixed>
     */
    public function updateKit(string $kitSku, array $compositions): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl(self::BASE.'/kits/'.rawurlencode($kitSku)), [], ['compositions' => array_values($compositions)]);
    }

    /**
     * Exclui kit (DELETE .../kits/{id}).
     *
     * @return array<string, mixed>
     */
    public function deleteKit(string $kitSku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->servicesUrl(self::BASE.'/kits/'.rawurlencode($kitSku)));
    }

    // ------------------------------------------------ departamentos fiscais

    /**
     * Departamentos fiscais (GET .../tax-departments).
     *
     * @return array<string, mixed>
     */
    public function listTaxDepartments(int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE.'/tax-departments'), ['_limit' => $limit, '_offset' => $offset]);
    }

    /**
     * Departamento com suas regras (GET .../tax-departments/{id}).
     *
     * @return array<string, mixed>
     */
    public function getTaxDepartment(string $taxDepartmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl(self::BASE."/tax-departments/{$taxDepartmentId}"));
    }

    /**
     * Cria departamento fiscal (POST .../tax-departments).
     *
     * `taxRules[]`: {customer_type, jurisdiction, federal_taxes[], destinations[]}.
     *
     * @param list<array<string, mixed>> $taxRules
     * @return array<string, mixed>
     */
    public function createTaxDepartment(string $description, array $taxRules): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl(self::BASE.'/tax-departments'), [], [
            'tax_department' => ['description' => $description],
            'tax_rules' => array_values($taxRules),
        ]);
    }

    /**
     * Cria varios departamentos (POST .../tax-departments/batch).
     *
     * @param list<array{tax_department: array{description: string}, tax_rules: list<array<string, mixed>>}> $taxDepartments
     * @return array<string, mixed>
     */
    public function createTaxDepartmentsBatch(array $taxDepartments): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl(self::BASE.'/tax-departments/batch'), [], ['tax_departments' => array_values($taxDepartments)]);
    }

    /**
     * Renomeia departamento (PATCH .../tax-departments/{id}).
     *
     * @return array<string, mixed>
     */
    public function renameTaxDepartment(string $taxDepartmentId, string $description): array
    {
        return $this->makeRequest(HttpMethod::PATCH, $this->servicesUrl(self::BASE."/tax-departments/{$taxDepartmentId}"), [], ['description' => $description]);
    }

    /**
     * Exclui departamento (DELETE .../tax-departments/{id}).
     *
     * @return array<string, mixed>
     */
    public function deleteTaxDepartment(string $taxDepartmentId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->servicesUrl(self::BASE."/tax-departments/{$taxDepartmentId}"));
    }

    // ------------------------------------------------------------ impostos

    /**
     * Impostos FEDERAIS do tipo no departamento
     * (PUT .../taxes/department/{dep}/types/{type}).
     *
     * @param list<array{type: string, aliquot: int|float, currency: string, normalizer: int, metadata?: array<string, mixed>}> $data type: IPI|PIS|COFINS
     * @return array<string, mixed>
     */
    public function updateFederalTaxes(string $taxDepartmentId, string $taxTypeId, array $data): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            $this->servicesUrl(self::BASE."/taxes/department/{$taxDepartmentId}/types/{$taxTypeId}"),
            [],
            ['data' => array_values($data)],
        );
    }

    /**
     * Impostos INTERESTADUAIS (ICMS/FECP)
     * (PATCH .../taxes/department/{dep}/types/{type}/interstate/{interstate}).
     *
     * @param list<array{type: string, aliquot: int|float, currency: string, normalizer: int, metadata?: array<string, mixed>}> $data type: ICMS|FECP
     * @return array<string, mixed>
     */
    public function updateInterstateTaxes(string $taxDepartmentId, string $taxTypeId, string $taxInterstateId, array $data): array
    {
        return $this->makeRequest(
            HttpMethod::PATCH,
            $this->servicesUrl(self::BASE."/taxes/department/{$taxDepartmentId}/types/{$taxTypeId}/interstate/{$taxInterstateId}"),
            [],
            ['data' => array_values($data)],
        );
    }
}
