<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Customer;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Clientes (`/v1/cliente`) e grupos de cliente (`/v1/grupo`). Paginação `limit/offset`.
 */
class CustomerMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'cliente/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "cliente/{$id}/");
    }

    /**
     * Busca por e-mail (`cliente/search?cliente_email=`).
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function search(?string $email = null, array $filters = []): array
    {
        $query = $filters;
        if ($email !== null) {
            $query['cliente_email'] = $email;
        }

        return $this->makeRequest(HttpMethod::GET, 'cliente/search/', $query);
    }

    /**
     * @param array<string,mixed> $data email, nome, tipo (PF/PJ), cpf/cnpj, telefones, enderecos[], grupo_id
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'cliente/', [], $data);
    }

    /** Troca o grupo do cliente pelo NOME do grupo. @return array<string,mixed> */
    public function updateGroup(int|string $id, string $grupo): array
    {
        return $this->makeRequest(HttpMethod::PUT, "cliente/{$id}/grupo/", [], ['grupo' => $grupo]);
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function listGroups(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'grupo/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function getGroup(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "grupo/{$id}/");
    }
}
