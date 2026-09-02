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

    // ── Recursos da conta (doc "Usuarios e Aplicativos" / "Consulta de usuarios") ──

    /** Dados de um aplicativo (client) pelo app_id: nome, site, scopes, callback. */
    public function application(int|string $appId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/applications/{$appId}");
    }

    /**
     * Notificacoes que o app NAO conseguiu receber (feeds perdidos). Query
     * extra aceita topic/limit/offset.
     *
     * @param  array<string,mixed>  $query
     */
    public function missedFeeds(int|string $appId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/missed_feeds', array_merge(['app_id' => $appId], $query));
    }

    /**
     * Revoga a autorizacao do app na conta do usuario (o refresh token morre).
     */
    public function revokeApplication(int|string $userId, int|string $appId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/users/{$userId}/applications/{$appId}");
    }

    /** Meios de pagamento aceitos pelo usuario (lista `{id, name, payment_type_id, ...}`). */
    public function acceptedPaymentMethods(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/accepted_payment_methods");
    }

    /** Enderecos cadastrados do usuario (exige token do proprio usuario). */
    public function addresses(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/addresses");
    }

    /**
     * Se o listing type esta disponivel pro usuario numa categoria:
     * `{available, cause?, code?, mapping}`. `listingType` ex.: gold_special.
     */
    public function availableListingType(int|string $userId, string $listingType, string $categoryId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/available_listing_type/".rawurlencode($listingType),
            ['category_id' => $categoryId],
        );
    }

    // ── Favoritos (bookmarks) ─────────────────────────────────────────────

    /** Favoritos do usuario — lista `[{item_id, bookmarked_date}]`. Use `me`. */
    public function bookmarks(int|string $userId = 'me'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/bookmarks");
    }

    /** Adiciona um item aos favoritos. */
    public function addBookmark(string $itemId, int|string $userId = 'me'): array
    {
        return $this->makeRequest(HttpMethod::POST, "/users/{$userId}/bookmarks", [], ['item_id' => $itemId]);
    }

    /** Remove um item dos favoritos — devolve o bookmark apagado. */
    public function removeBookmark(string $itemId, int|string $userId = 'me'): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/users/{$userId}/bookmarks/{$itemId}");
    }

    // ── Classificados (pacotes de promocao) ───────────────────────────────

    /** Pacotes de promocao de classificados contratados pelo usuario. */
    public function classifiedsPromotionPacks(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/classifieds_promotion_packs");
    }

    /**
     * Se ainda ha publicacoes disponiveis num pacote (ex.: silver) pra
     * categoria: `{has_available_listings}`. Atencao: o param e `categoryId`
     * em camelCase, diferente do resto da API.
     */
    public function classifiedsPromotionPackAvailability(int|string $userId, string $listingType, string $categoryId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/classifieds_promotion_packs/".rawurlencode($listingType),
            ['categoryId' => $categoryId],
        );
    }

    // ── Pagamento imediato (so Mercado Pago) ──────────────────────────────

    /** Marca a conta pra aceitar SO Mercado Pago (desliga "acordar com o vendedor"). */
    public function setImmediatePayment(int|string $userId, string $reason = 'by_user'): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/users/{$userId}/immediate_payment", [], ['reason' => $reason]);
    }

    /** Remove a marca de pagamento imediato colocada pelo usuario. */
    public function removeImmediatePayment(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/users/{$userId}/immediate_payment/by_user");
    }

    // ── Lojas oficiais (brands) ───────────────────────────────────────────

    /**
     * Marcas/lojas oficiais vinculadas ao usuario: `{status: LINKED|...,
     * cust_id, site_id, brands: [{official_store_id, name, ...}]}`.
     * Usuario inexistente = 404 U00002.
     */
    public function brands(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/brands");
    }

    /**
     * Detalhe de uma loja oficial do usuario. `officialStoreId` so digitos
     * (400 invalid_parameter se nao); nao vinculada = 404 B00001.
     */
    public function brand(int|string $userId, int|string $officialStoreId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/brands/{$officialStoreId}");
    }
}
