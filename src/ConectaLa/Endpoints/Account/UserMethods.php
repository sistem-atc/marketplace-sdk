<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Account;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Users (cadastro). */
class UserMethods extends BaseMethods
{
    /** Lista (GET /Users). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Users', $filters);
    }

    /** Cria (POST /Users). */
    public function create(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Users', body: $body);
    }

    /** Atualiza (PUT /Users/{id}). */
    public function update(string $id, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Users/{$id}", body: $body);
    }
}
