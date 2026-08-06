<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Account;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Stores (cadastro). */
class StoreMethods extends BaseMethods
{
    /** Lista (GET /Stores). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Stores', $filters);
    }

    /** Cria (POST /Stores). */
    public function create(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Stores', body: $body);
    }

    /** Atualiza (PUT /Stores/{id}). */
    public function update(string $id, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Stores/{$id}", body: $body);
    }
}
