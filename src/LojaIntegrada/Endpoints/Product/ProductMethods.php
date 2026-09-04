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

    /**
     * `id_externo=1` busca pelo id externo; `descricao_completa=1` traz a descrição HTML.
     *
     * @return array<string,mixed>
     */
    public function get(int|string $id, bool $byExternalId = false, bool $fullDescription = false): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto/{$id}/", $this->lookupQuery($byExternalId, $fullDescription));
    }

    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'produto/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int|string $id, array $data, bool $byExternalId = false, bool $fullDescription = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto/{$id}/", $this->lookupQuery($byExternalId, $fullDescription), $data);
    }

    public function delete(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "produto/{$id}/");
    }

    /**
     * Altera a URL (alias) do produto; `replace_main=1` substitui a URL principal.
     *
     * @return array<string,mixed>
     */
    public function updateAlias(int|string $id, string $absolutePath, bool $replaceMain = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto/{$id}/alias/", $replaceMain ? ['replace_main' => 1] : [], ['absolute_path' => $absolutePath]);
    }

    /** @return array<string,int> */
    private function lookupQuery(bool $byExternalId, bool $fullDescription): array
    {
        $query = [];
        if ($byExternalId) {
            $query['id_externo'] = 1;
        }
        if ($fullDescription) {
            $query['descricao_completa'] = 1;
        }

        return $query;
    }
}
