<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Coupon;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Cupons de desconto (`/v1/cupom`). Paginação `limit/offset`.
 */
class CouponMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'cupom/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "cupom/{$id}/");
    }

    /**
     * @param array<string,mixed> $data codigo, tipo (porcentagem/fixo/frete_gratis), valor, validade, quantidade, produtos[], grupos[]/clientes[]
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'cupom/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int|string $id, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "cupom/{$id}/", [], $data);
    }
}
