<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Product;

use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        $query = array_merge(['limit' => $limit, 'offset' => $offset], $filters);
        return $this->makeRequest(HttpMethod::GET, 'produto/', $query);
    }

    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto/{$id}/");
    }

    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'produto/', [], $data);
    }

    public function update(int|string $id, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto/{$id}/", [], $data);
    }

    public function delete(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "produto/{$id}/");
    }
}
