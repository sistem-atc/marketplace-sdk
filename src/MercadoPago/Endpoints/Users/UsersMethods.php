<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Users;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Users — a conta dona do token. Serve pra descobrir `id` (collector_id /
 * user_id do seller), `site_id`, `email` e `nickname` logo apos o OAuth,
 * e pra validar que a integration autenticou na conta certa antes de
 * gravar credenciais na company.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/users/_users_me/get
 */
class UsersMethods extends BaseMethods
{
    /**
     * @return array<string, mixed>
     */
    public function me(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users/me');
    }
}
