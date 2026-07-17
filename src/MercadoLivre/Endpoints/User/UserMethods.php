<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\User;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User\UserResponseDTO;

class UserMethods extends BaseMethods
{
    /**
     * Usuario autenticado (GET /users/me) — a fonte do seller_id.
     * `toArray()` e' LOSSLESS.
     */
    public function me(): UserResponseDTO
    {
        return UserResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/users/me'));
    }

    /** Usuario por id (GET /users/{id}) — mesma arvore do me(). */
    public function get(int|string $userId): UserResponseDTO
    {
        return UserResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/users/{$userId}"));
    }
}
