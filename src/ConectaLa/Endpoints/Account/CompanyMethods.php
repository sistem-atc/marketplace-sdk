<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Account;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Companies (cadastro). */
class CompanyMethods extends BaseMethods
{
    /** Lista (GET /Companies). */
    public function list(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Companies', $filters);
    }

    /** Cria (POST /Companies). */
    public function create(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Companies', body: $body);
    }

    /** Atualiza (PUT /Companies/{id}). */
    public function update(string $id, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Companies/{$id}", body: $body);
    }
}
