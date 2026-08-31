<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerService;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\AgentSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\ConversationMessagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\CreatedConversationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\CustomerServicePerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\SentMessageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\SessionSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService\UploadedImage;

/**
 * TikTok Shop Customer Service / IM API (versao /customer_service/202309/...).
 *
 * Chat buyer<->shop (pre-venda inclusive; NAO e' por pedido). Identidade do
 * comprador vem dos `participants[]` da lista de conversas (role BUYER/SELLER +
 * nickname) — o webhook type 33 so' entrega `sender_im_user_id`. Vinculo com
 * pedido e' por mensagem (ORDER_CARD -> order_id; PRODUCT_CARD -> product_id).
 *
 * makeRequest ja' assina (app_key/sign/shop_cipher/timestamp) + retry + refresh.
 *
 * Sobre as VERSOES: o TikTok versiona endpoint a endpoint e nesta familia elas
 * divergiram (lista de conversas em 202309, detalhe em 202601, envio de
 * mensagem em 202606, sessoes em 202602/202605). Cada metodo carrega a versao
 * da SUA doc; `DEFAULT_VERSION` so' vale pros que continuam em 202309.
 */
class CustomerServiceMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    /** Get Conversation (detalhe) — a LISTA continua em 202309. */
    private const CONVERSATION_DETAIL_VERSION = '202601';

    /** Send Message. */
    private const SEND_MESSAGE_VERSION = '202606';

    /** Search Sessions. */
    private const SESSION_SEARCH_VERSION = '202602';

    /** End Session. */
    private const END_SESSION_VERSION = '202605';

    /** Get Customer Service Performance. */
    private const PERFORMANCE_VERSION = '202407';

    /** Teto de conversas por pagina imposto pelo TikTok. */
    private const MAX_CONVERSATIONS_PAGE_SIZE = 20;

    /** Teto de mensagens por pagina — 10, METADE do teto das conversas. */
    private const MAX_MESSAGES_PAGE_SIZE = 10;

    /**
     * Lista conversas. Resposta em `data.conversations[]` (id, unread_count,
     * latest_message, participants[]{role, im_user_id, nickname, avatar}).
     *
     * `needSessionId`/`needSessionInfo` sao opt-in: sem eles a conversa volta
     * sem `cur_session*` e nao da' pra saber se ha atendimento em andamento.
     */
    public function getConversations(
        int $pageSize = 20,
        string $pageToken = '',
        string $locale = 'pt-BR',
        bool $needSessionId = false,
        bool $needSessionInfo = false,
        string $version = self::DEFAULT_VERSION,
    ): ConversationListResponseDTO {
        $query = [
            'page_size' => min($pageSize, self::MAX_CONVERSATIONS_PAGE_SIZE),
            'locale' => $locale,
            'need_session_id' => $needSessionId,
            'need_session_info' => $needSessionInfo,
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(HttpMethod::GET, "/customer_service/{$version}/conversations", $query);

        return ConversationListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Historico de mensagens de uma conversa. `data.messages[]` (id, type,
     * content, create_time, sender{im_user_id, role, nickname}).
     *
     * `needData`/`needPlaintext` trazem o card ja' estruturado e a versao
     * legivel — sem eles o consumidor precisa parsear `content` na mao.
     */
    public function getConversationMessages(
        string $conversationId,
        int $pageSize = 10,
        string $pageToken = '',
        string $sortOrder = 'DESC',
        string $locale = 'pt-BR',
        string $sortField = 'create_time',
        bool $needData = false,
        bool $needPlaintext = false,
        ?string $timeZone = null,
        string $version = self::DEFAULT_VERSION,
    ): ConversationMessagesResponseDTO {
        $query = [
            'page_size' => min($pageSize, self::MAX_MESSAGES_PAGE_SIZE),
            'sort_order' => $sortOrder,
            'sort_field' => $sortField,
            'locale' => $locale,
            'need_data' => $needData,
            'need_plaintext' => $needPlaintext,
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }
        if ($timeZone !== null) {
            $query['time_zone'] = $timeZone;
        }

        $response = $this->makeRequest(HttpMethod::GET, "/customer_service/{$version}/conversations/{$conversationId}/messages", $query);

        return ConversationMessagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Envia mensagem numa conversa.
     *
     * @param  string  $type  TEXT | IMAGE | VIDEO | PRODUCT_CARD | ORDER_CARD | RETURN_REFUND_CARD | COUPON_CARD | LOGISTICS_CARD
     * @param  string  $content  pra TEXT = JSON {"content":"texto"}; pra IMAGE = a URL devolvida por uploadBuyerMessagesImage
     * @param  string|null  $senderRole  CUSTOMER_SERVICE | SYSTEM | ROBOT — quem "assina" a mensagem no chat do comprador
     */
    public function sendMessage(
        string $conversationId,
        string $type,
        string $content,
        ?string $senderRole = null,
        string $version = self::SEND_MESSAGE_VERSION,
    ): SentMessageResponseDTO {
        $body = ['type' => $type, 'content' => $content];
        if ($senderRole !== null) {
            $body['sender_role'] = $senderRole;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations/{$conversationId}/messages",
            [],
            $body,
        );

        return SentMessageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Atalho pra enviar texto (embrulha no content JSON esperado).
     *
     * Max 2000 caracteres — acima disso o TikTok recusa a mensagem.
     */
    public function sendText(
        string $conversationId,
        string $text,
        ?string $senderRole = null,
        string $version = self::SEND_MESSAGE_VERSION,
    ): SentMessageResponseDTO {
        return $this->sendMessage($conversationId, 'TEXT', json_encode(['content' => $text]), $senderRole, $version);
    }

    /**
     * Cria/abre uma conversa com um comprador. Retorna o conversation_id.
     *
     * `$buyerUserId` e' o `user_id` do PEDIDO (Get Order Detail), NAO o
     * `im_user_id` que aparece nos participantes da conversa.
     */
    public function createConversation(
        string $buyerUserId,
        string $version = self::DEFAULT_VERSION,
    ): CreatedConversationResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations",
            [],
            ['buyer_user_id' => $buyerUserId],
        );

        return CreatedConversationResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Marca as mensagens da conversa como lidas.
     *
     * Resposta e' `data: {}` — nao ha' DTO; sucesso e' nao ter estourado.
     *
     * @return array<string, mixed>
     */
    public function readMessages(string $conversationId, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations/{$conversationId}/messages/read",
            [],
            [],
        );
    }
    /**
     * Sobe uma imagem pro chat e devolve a URL hospedada pelo TikTok.
     *
     * Fluxo em DOIS passos: este upload NÃO envia nada pro comprador — pegue a
     * `url` do retorno e passe em sendMessage(type: 'IMAGE'). Formatos aceitos:
     * jpg, gif, webp, png; até 10 MB.
     *
     * Duas armadilhas neste endpoint:
     * 1. A tabela da doc diz "GET"; o curl e os SDKs usam POST multipart. Vale
     *    o POST.
     * 2. O corpo é multipart, e por isso NÃO entra na assinatura — daí a
     *    chamada não passar pelo makeRequest (que assina o body JSON) e sim
     *    montar a query assinada com body null.
     *
     * @param  string  $filePath  caminho local do arquivo
     */
    public function uploadBuyerMessagesImage(
        string $filePath,
        string $version = self::DEFAULT_VERSION,
    ): UploadedImage {
        $apiPath = "/customer_service/{$version}/images/upload";
        $query = $this->buildSignedQuery($apiPath, [], null);

        $response = $this->httpClient
            ->attach('data', file_get_contents($filePath), basename($filePath))
            ->post($apiPath.'?'.http_build_query($query));

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return UploadedImage::fromArray($data['data'] ?? []);
    }

    /**
     * Detalhe de UMA conversa (versao 202601).
     *
     * NAO e' simetrico com a lista: o detalhe nao traz `latest_message` nem
     * `can_send_message`, e renomeia `buyer_platform` do participante pra
     * `platform`. Pra saber se a loja ainda pode responder, a fonte e' a LISTA.
     */
    public function getConversation(
        string $conversationId,
        bool $needSessionId = false,
        bool $needSessionInfo = false,
        string $version = self::CONVERSATION_DETAIL_VERSION,
    ): ConversationDetailResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/customer_service/{$version}/conversations/{$conversationId}",
            ['need_session_id' => $needSessionId, 'need_session_info' => $needSessionInfo],
        );

        return ConversationDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Encerra a SESSAO de atendimento em andamento numa conversa.
     *
     * Encerra a sessao, nao a conversa: o comprador pode voltar a escrever e
     * uma sessao nova abre no mesmo `conversation_id`. Resposta e' `data: {}`.
     *
     * @return array<string, mixed>
     */
    public function endSession(
        string $conversationId,
        string $sessionId,
        string $version = self::END_SESSION_VERSION,
    ): array {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/conversations/{$conversationId}/sessions/{$sessionId}/end",
            [],
            [],
        );
    }

    /**
     * Busca sessoes de atendimento numa janela de tempo, com as metricas de SLA
     * (tempo de 1a resposta, nota de satisfacao, tags do atendimento).
     *
     * Janela em epoch de SEGUNDOS e limitada aos ultimos 90 DIAS pelo TikTok —
     * nao existe backfill mais antigo por esta API.
     */
    public function searchSessions(
        int $beginTimeGe,
        int $beginTimeLt,
        int $pageSize = 20,
        string $pageToken = '',
        ?string $buyerNickname = null,
        string $locale = 'pt-BR',
        string $version = self::SESSION_SEARCH_VERSION,
    ): SessionSearchResponseDTO {
        $query = ['page_size' => $pageSize, 'locale' => $locale];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = ['begin_time_ge' => $beginTimeGe, 'begin_time_lt' => $beginTimeLt];
        if ($buyerNickname !== null) {
            $body['buyer_nickname'] = $buyerNickname;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/sessions/search",
            $query,
            $body,
        );

        return SessionSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Le a configuracao do atendente autenticado. */
    public function getAgentSettings(string $version = self::DEFAULT_VERSION): AgentSettingsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/customer_service/{$version}/agents/settings");

        return AgentSettingsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Liga/desliga a atribuicao AUTOMATICA de conversas pro atendente.
     *
     * Com `false` as conversas novas so' chegam por atribuicao manual no Seller
     * Center — integracao que consome a IM API quer `true`, senao a caixa de
     * entrada fica vazia sem erro nenhum. Resposta e' `data: {}`.
     *
     * @return array<string, mixed>
     */
    public function updateAgentSettings(bool $canAcceptChat, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/customer_service/{$version}/agents/settings",
            [],
            ['can_accept_chat' => $canAcceptChat],
        );
    }

    /**
     * Metricas de atendimento da LOJA no periodo.
     *
     * As datas aqui sao STRING `YYYY-MM-DD` (nao epoch, ao contrario de todo o
     * resto da familia) e o recorte e' pela data da SESSAO.
     */
    public function getPerformance(
        string $supportDateGe,
        string $supportDateLt,
        string $version = self::PERFORMANCE_VERSION,
    ): CustomerServicePerformanceResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/customer_service/{$version}/performance",
            ['support_date_ge' => $supportDateGe, 'support_date_lt' => $supportDateLt],
        );

        return CustomerServicePerformanceResponseDTO::fromArray($response['data'] ?? []);
    }
}
