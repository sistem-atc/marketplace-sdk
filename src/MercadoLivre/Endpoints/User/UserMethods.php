<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\User;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class UserMethods extends BaseMethods
{
    public function me(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users/me');
    }

    public function get(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}");
    }
}
