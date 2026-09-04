<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Netshoes\Bases\BaseMethods;

/**
 * Tabelas de dominio do catalogo Netshoes — Swagger V1, tag Products Templates.
 * Sao os valores validos pra montar o ProductRequest da V2 (brand, color,
 * flavor, size, department, productType, attributes).
 *
 *   GET /api/v1/brands | /colors | /flavors | /sizes | /sellers
 *   GET /api/v1/bus/{buId}/departments
 *   GET /api/v1/department/{departmentCode}/productType
 *   GET /api/v1/department/{departmentCode}/productType/{productTypeCode}/templates
 *
 * Listas vem em {items[], links[]}; cada item = {code|id, name, externalCode}.
 */
class CatalogMethods extends BaseMethods
{
    /** @return array<string, mixed> BrandListResource */
    public function listBrands(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/brands');
    }

    /** @return array<string, mixed> ColorListResource */
    public function listColors(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/colors');
    }

    /** @return array<string, mixed> FlavorListResource */
    public function listFlavors(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/flavors');
    }

    /** @return array<string, mixed> SizeListResource */
    public function listSizes(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/sizes');
    }

    /** @return array<int|string, mixed> SellerResource[] {code, name} */
    public function listSellers(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/sellers');
    }

    /**
     * Departamentos de uma unidade de negocio (buId: Netshoes | Zattini).
     *
     * @return array<string, mixed>  DepartmentListResource
     */
    public function listDepartments(string $buId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/bus/'.rawurlencode($buId).'/departments');
    }

    /** @return array<string, mixed> ProductTypeListResource */
    public function listProductTypes(string|int $departmentCode): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/api/v1/department/'.rawurlencode((string) $departmentCode).'/productType',
        );
    }

    /**
     * Atributos (template) exigidos por departamento x tipo de produto.
     *
     * @return array<string, mixed>  TemplateListResource
     */
    public function listTemplates(string|int $departmentCode, string|int $productTypeCode): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/api/v1/department/'.rawurlencode((string) $departmentCode)
                .'/productType/'.rawurlencode((string) $productTypeCode).'/templates',
        );
    }
}
