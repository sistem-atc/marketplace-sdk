<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Livestream;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\UserApiMethods;
use SistemAtc\Marketplaces\Shopee\Support\HttpClientFactory;

/**
 * Modulo `/api/v2/livestream` — Shopee Live (sessoes, sacola de itens,
 * metricas em tempo real e comentarios).
 *
 * API de tipo **User**: autentica com `user_id` (ver UserApiMethods), nao
 * com `shop_id`. Por isso os itens da sacola carregam o proprio `shop_id`
 * no payload — um usuario pode transmitir com itens de mais de uma loja.
 *
 * ⚠️ Doc oficial marca todas as rotas como "For TW, ID, TH, PH, MY, SG, VN":
 * no BR a chamada pode voltar `error_permission`/`error_not_support`. Trate
 * como capacidade a validar, nao como garantida.
 *
 * Paginacao: `offset` + `page_size`; a resposta traz `next_offset`/`more`.
 * Todos os retornos sao o `response` cru da API (sem DTO).
 */
class LivestreamMethods extends UserApiMethods
{
    // ----------------------------------------------------------------- sessao

    /**
     * Sobe a imagem de capa da live (multipart, campo `image`).
     * Devolve `['image_url' => ...]` pra usar em createSession/updateSession.
     *
     * Sai fora do makeRequest: o cliente da factory e' asJson() e o header
     * `Content-Type: application/json` sobreviveria ao attach(), entao o
     * cliente multipart e' montado do zero (mesmo padrao do TikTok).
     *
     * @return array<string, mixed>
     */
    public function uploadImage(string $contents, string $filename = 'cover.jpg'): array
    {
        $apiPath = '/api/v2/livestream/upload_image';
        $query = $this->signedUserQuery($apiPath);

        // make() garante o refresh do token antes de montar o cliente cru.
        HttpClientFactory::make($this->integration);

        $response = Http::baseUrl(config('marketplaces.shopee.base_url', 'https://partner.shopeemobile.com'))
            ->timeout(60)
            ->connectTimeout(10)
            ->acceptJson()
            ->attach('image', $contents, $filename)
            ->post($apiPath.'?'.http_build_query($query));

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) {
            $this->handleError($response);
        }

        return $data['response'] ?? [];
    }

    /**
     * Cria a sessao de live (nao inicia a transmissao — ver startSession).
     * `title`/`description` ate 200 chars; `coverImageUrl` vem do uploadImage.
     * Devolve `['session_id' => ...]`.
     *
     * @return array<string, mixed>
     */
    public function createSession(
        string $title,
        string $description,
        string $coverImageUrl,
        bool $isTest = false,
    ): array {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/create_session', [], [
            'title' => $title,
            'description' => $description,
            'cover_image_url' => $coverImageUrl,
            'is_test' => $isTest,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe da sessao: capa, titulo, descricao, tipo (teste/normal),
     * status, create/update time, url de stream (RTMP) e chave.
     *
     * @return array<string, mixed>
     */
    public function getSessionDetail(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_session_detail', [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza capa/titulo/descricao/tipo. Todos os campos sao obrigatorios
     * na API (nao e' PATCH): reenvie os valores atuais que nao mudam.
     *
     * @return array<string, mixed>
     */
    public function updateSession(
        int $sessionId,
        string $title,
        string $description,
        string $coverImageUrl,
        bool $isTest = false,
    ): array {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/update_session', [], [
            'session_id' => $sessionId,
            'title' => $title,
            'description' => $description,
            'cover_image_url' => $coverImageUrl,
            'is_test' => $isTest,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Inicia a transmissao. `domainId` = dominio de stream (vem do
     * getSessionDetail); `aiStream` so' vale na regiao PH.
     *
     * @return array<string, mixed>
     */
    public function startSession(int $sessionId, int $domainId, bool $aiStream = false): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/start_session', [], [
            'session_id' => $sessionId,
            'domain_id' => $domainId,
            'ai_stream' => $aiStream,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra a transmissao.
     *
     * @return array<string, mixed>
     */
    public function endSession(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/end_session', [], [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    // --------------------------------------------------------------- metricas

    /**
     * Indicadores em tempo real da sala: likes, comentarios, shares, views,
     * pico de audiencia, GMV, pedidos etc.
     *
     * @return array<string, mixed>
     */
    public function getSessionMetric(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_session_metric', [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Indicadores em tempo real POR ITEM da live (cliques, add-to-cart,
     * pedidos). Paginado por offset.
     *
     * @return array<string, mixed>
     */
    public function getSessionItemMetric(int $sessionId, int $offset = 0, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_session_item_metric', [
            'session_id' => $sessionId,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    // ------------------------------------------------------- sacola de itens

    /**
     * Quantidade de itens na sacola e o limite maximo permitido.
     *
     * @return array<string, mixed>
     */
    public function getItemCount(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_item_count', [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens da aba "My Likes" (candidatos a sacola). `keyword` filtra por nome.
     *
     * @return array<string, mixed>
     */
    public function getLikeItemList(int $offset = 0, int $pageSize = 20, ?string $keyword = null): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_like_item_list', array_filter([
            'offset' => $offset,
            'page_size' => $pageSize,
            'keyword' => $keyword,
        ], static fn ($v) => $v !== null));

        return $response['response'] ?? [];
    }

    /**
     * Itens da aba "Recently" (usados em lives recentes).
     *
     * @return array<string, mixed>
     */
    public function getRecentItemList(int $offset = 0, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_recent_item_list', [
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens a sacola. Cada item = `['item_id' => int, 'shop_id' => int]`.
     *
     * @param  list<array{item_id: int, shop_id: int}>  $itemList
     * @return array<string, mixed>
     */
    public function addItemList(int $sessionId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/add_item_list', [], [
            'session_id' => $sessionId,
            'item_list' => array_values($itemList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove itens da sacola. Mesmo formato do addItemList.
     *
     * @param  list<array{item_id: int, shop_id: int}>  $itemList
     * @return array<string, mixed>
     */
    public function deleteItemList(int $sessionId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/delete_item_list', [], [
            'session_id' => $sessionId,
            'item_list' => array_values($itemList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Itens da sacola com item_id, numero de serie (posicao), nome, preco.
     *
     * @return array<string, mixed>
     */
    public function getItemList(int $sessionId, int $offset = 0, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_item_list', [
            'session_id' => $sessionId,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Reordena a sacola: a ordem da lista enviada vira a ordem exibida.
     *
     * @param  list<array{item_id: int, shop_id: int}>  $itemList
     * @return array<string, mixed>
     */
    public function updateItemList(int $sessionId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/update_item_list', [], [
            'session_id' => $sessionId,
            'item_list' => array_values($itemList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Item em destaque ("showing") na tela da live.
     *
     * @return array<string, mixed>
     */
    public function getShowItem(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_show_item', [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Tira o item em destaque da tela.
     *
     * @return array<string, mixed>
     */
    public function deleteShowItem(int $sessionId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/delete_show_item', [], [
            'session_id' => $sessionId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Coloca um item da sacola em destaque na tela.
     *
     * @return array<string, mixed>
     */
    public function updateShowItem(int $sessionId, int $itemId, int $shopId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/update_show_item', [], [
            'session_id' => $sessionId,
            'item_id' => $itemId,
            'shop_id' => $shopId,
        ]);

        return $response['response'] ?? [];
    }

    // ------------------------------------------------------ conjuntos (sets)

    /**
     * Conjuntos de produtos salvos (nome, id, quantidade de itens).
     *
     * @return array<string, mixed>
     */
    public function getItemSetList(int $offset = 0, int $pageSize = 20, ?string $keyword = null): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_item_set_list', array_filter([
            'offset' => $offset,
            'page_size' => $pageSize,
            'keyword' => $keyword,
        ], static fn ($v) => $v !== null));

        return $response['response'] ?? [];
    }

    /**
     * Itens de um conjunto.
     *
     * @return array<string, mixed>
     */
    public function getItemSetItemList(int $itemSetId, int $offset = 0, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_item_set_item_list', [
            'item_set_id' => $itemSetId,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Joga conjuntos inteiros na sacola da sessao.
     *
     * @param  list<int>  $itemSetIds
     * @return array<string, mixed>
     */
    public function applyItemSet(int $sessionId, array $itemSetIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/apply_item_set', [], [
            'session_id' => $sessionId,
            'item_set_ids' => array_values($itemSetIds),
        ]);

        return $response['response'] ?? [];
    }

    // ------------------------------------------------------------ comentarios

    /**
     * Comentarios dos ULTIMOS 10 SEGUNDOS da sala (user_id, username,
     * comment_id, content, timestamp). Use `next_offset` da resposta como
     * `offset` da proxima chamada pra nao repetir — e' um poll, nao historico.
     *
     * @return array<string, mixed>
     */
    public function getLatestCommentList(int $sessionId, int $offset = 0): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/livestream/get_latest_comment_list', [
            'session_id' => $sessionId,
            'offset' => $offset,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Publica comentario como streamer (ate 150 chars).
     *
     * @return array<string, mixed>
     */
    public function postComment(int $sessionId, string $content): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/post_comment', [], [
            'session_id' => $sessionId,
            'content' => $content,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Bloqueia um usuario de comentar na sessao.
     *
     * @return array<string, mixed>
     */
    public function banUserComment(int $sessionId, int $banUserId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/ban_user_comment', [], [
            'session_id' => $sessionId,
            'ban_user_id' => $banUserId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Desbloqueia um usuario pra comentar.
     *
     * @return array<string, mixed>
     */
    public function unbanUserComment(int $sessionId, int $unbanUserId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/livestream/unban_user_comment', [], [
            'session_id' => $sessionId,
            'unban_user_id' => $unbanUserId,
        ]);

        return $response['response'] ?? [];
    }
}
