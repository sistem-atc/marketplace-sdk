<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Site;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Recursos "globais" do site: sites, dump de categorias, custos por vender,
 * busca pública, meios de pagamento, moedas, CEPs, comunicações do ML,
 * multiget de usuários, bloqueios e usuário de teste.
 */
class SiteMethods extends BaseMethods
{
    /**
     * Todos os sites do ML (GET /sites): `[{id, name, default_currency_id}]`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function sites(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites');
    }

    /**
     * Dump completo da árvore de categorias (GET /sites/{id}/categories/all).
     * Vem gzip (o client descomprime) e é GRANDE — dezenas de MB com
     * `withAttributes=true`. Headers `X-Content-Created` / `X-Content-MD5`
     * dizem quando foi gerado; use categoriesDumpHeaders() antes de baixar
     * de novo.
     *
     * @return array<string, mixed>
     */
    public function categoriesDump(string $siteId = 'MLB', bool $withAttributes = false): array
    {
        $query = $withAttributes ? ['withAttributes' => 'true'] : [];

        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/categories/all', $query);
    }

    /**
     * Só os headers do dump (HEAD /sites/{id}/categories/all):
     * `{created, md5}` — pra saber se mudou sem baixar o arquivo.
     *
     * @return array{created: ?string, md5: ?string}
     */
    public function categoriesDumpHeaders(string $siteId = 'MLB'): array
    {
        $response = $this->httpClient->head('/sites/'.rawurlencode($siteId).'/categories/all');

        if ($response->failed()) {
            $this->handleError($response);
        }

        return [
            'created' => $response->header('X-Content-Created') ?: null,
            'md5' => $response->header('X-Content-MD5') ?: null,
        ];
    }

    /**
     * Custo por vender / tipos de anúncio pra um preço
     * (GET /sites/{id}/listing_prices?price=...). Query aceita `price`
     * (obrigatório), `listing_type_id`, `category_id`, `currency_id`,
     * `quantity`, `tags` (ahora-3, supermarket_eligible...), `logistic_type`,
     * `shipping_mode` e `billable_weight` — sem os três últimos o fixed fee
     * NÃO bate com o cobrado. Com `listing_type_id` a resposta é um objeto;
     * sem, é uma lista por tipo.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listingPrices(string $siteId, float|int|string $price, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/sites/'.rawurlencode($siteId).'/listing_prices',
            array_merge(['price' => $price], $query),
        );
    }

    /**
     * Busca pública de anúncios ativos (GET /sites/{id}/search). Filtros:
     * `seller_id`, `nickname`, `category`, `q`, `sort` (price_asc...),
     * `shipping_cost=free`, `offset`, `limit`. Só itens ativos; a resposta
     * traz `available_sorts` e `available_filters`.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function search(string $siteId, array $query): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/search', $query);
    }

    /**
     * Meios de pagamento do site (GET /sites/{id}/payment_methods):
     * `[{id, name, payment_type_id, thumbnail, secure_thumbnail}]`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function paymentMethods(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/payment_methods');
    }

    /**
     * Detalhe de um meio de pagamento (GET /sites/{id}/payment_methods/{methodId}):
     * card_issuer, payer_costs (parcelas), settings, exceptions_by_card.
     *
     * @return array<string, mixed>
     */
    public function paymentMethod(string $siteId, string $paymentMethodId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/sites/'.rawurlencode($siteId).'/payment_methods/'.rawurlencode($paymentMethodId),
        );
    }

    /**
     * Todas as moedas (GET /currencies): `[{id, description, symbol, decimal_places}]`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function currencies(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/currencies');
    }

    /**
     * Detalhe de uma moeda (GET /currencies/{id}).
     *
     * @return array<string, mixed>
     */
    public function currency(string $currencyId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/currencies/'.rawurlencode($currencyId));
    }

    /**
     * Localização de um CEP (GET /countries/{countryId}/zip_codes/{zip}):
     * `{zip_code, city{id,name}, state{id,name}, country{id,name}}`.
     * `countryId` é ISO-2 (BR, AR...), não o site.
     *
     * @return array<string, mixed>
     */
    public function zipCode(string $countryId, string $zipCode): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/countries/'.rawurlencode($countryId).'/zip_codes/'.rawurlencode($zipCode),
        );
    }

    /**
     * CEPs entre dois valores (GET /country/{countryId}/zip_codes/search_between).
     * Atenção: o path é `/country/` no singular, diferente do zipCode().
     *
     * @return array<string, mixed>
     */
    public function zipCodesBetween(string $countryId, string $from, string $to): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/country/'.rawurlencode($countryId).'/zip_codes/search_between',
            ['zip_code_from' => $from, 'zip_code_to' => $to],
        );
    }

    /**
     * Comunicados do ML pro usuário do token (GET /communications/notices?limit&offset):
     * `{paging, results[]}`. Com token do seller vêm avisos comerciais; com
     * token do dono do app vêm avisos da integração. Só os vigentes, mais
     * recentes primeiro.
     *
     * @return array<string, mixed>
     */
    public function notices(int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/communications/notices', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * Multiget de usuários (GET /users?ids=a,b): `[{code, body{...}}]` por id.
     *
     * @param  array<int, int|string>  $userIds
     * @return array<int, array<string, mixed>>
     */
    public function usersMultiGet(array $userIds): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users', ['ids' => implode(',', $userIds)]);
    }

    /**
     * Usuários bloqueados pelo seller (GET /block-api/search/users/{id}?type=...).
     * `type=blocked_by_order` lista bloqueios feitos a partir de vendas:
     * `{users[{id, blocked_at}], paging}`.
     *
     * @return array<string, mixed>
     */
    public function blockedUsers(int|string $userId, string $type = 'blocked_by_order'): array
    {
        return $this->makeRequest(HttpMethod::GET, "/block-api/search/users/{$userId}", ['type' => $type]);
    }

    /**
     * Cria usuário de teste (POST /users/test_user, body `{site_id}`):
     * `{id, nickname, password, site_status}`. Máximo 10 por conta e o ML
     * NÃO lista os já criados — guarde as credenciais na hora.
     *
     * @return array<string, mixed>
     */
    public function createTestUser(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/users/test_user', [], ['site_id' => $siteId]);
    }
}
