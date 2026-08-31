<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\CreatedEngagementTaskResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\EngagementPermissionsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\EngagementTaskPerformancesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\MessageTemplatesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement\SendEngagementMessageResponseDTO;

/**
 * TikTok Shop Customer Engagement API — disparo de mensagem em MASSA pra base
 * de compradores da loja (retencao/reengajamento), no canal TIKTOK_IM.
 *
 * NAO confundir com a Customer Service / IM API: la' e' conversa 1:1 iniciada
 * pelo comprador; aqui a loja dispara pra uma lista e a mensagem cai no MESMO
 * inbox do TikTok. As duas alimentam a projecao de chats do ERP, mas so' a
 * Customer Service devolve o historico.
 *
 * Fluxo obrigatorio, nesta ordem:
 *   1. getPermissions()      — a feature e' liberada loja a loja
 *   2. getMessageTemplates() — ou createCustomTask() se tiver CUSTOM_MSG
 *   3. createTask()          — devolve o task_id
 *   4. sendMessage()         — dispara pros compradores, amarrado ao task_id
 *   5. getTaskPerformances() — leitura do resultado
 */
class CustomerEngagementMethods extends BaseMethods
{
    /** Templates, tasks, envio e performance. */
    private const DEFAULT_VERSION = '202412';

    /** Permissoes e task com mensagem propria vieram depois. */
    private const V202502 = '202502';

    /**
     * Features de engajamento liberadas pra loja.
     *
     * Chamar ANTES de qualquer outra coisa: sem `FUNDAMENTAL` autorizado, toda
     * a familia recusa — e o erro nao diz que o problema e' permissao.
     */
    public function getPermissions(string $version = self::V202502): EngagementPermissionsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/customer_engagement/{$version}/permissions");

        return EngagementPermissionsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Templates de mensagem predefinidos pelo TikTok.
     *
     * O texto vem PRONTO no locale pedido e nao e' editavel; o que da' pra
     * escolher e' quais produtos/cupons entram como card, dentro dos limites
     * de `product_card_rules`/`coupon_card_rules` do proprio template.
     *
     * @param  string  $locale  codigos BCP-47 separados por virgula (ex.: 'pt-BR')
     */
    public function getMessageTemplates(string $locale = 'pt-BR', string $version = self::DEFAULT_VERSION): MessageTemplatesResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/customer_engagement/{$version}/message_templates",
            ['locale' => $locale],
        );

        return MessageTemplatesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cria a task de engajamento a partir de um TEMPLATE do TikTok.
     *
     * `idempotencyKey` vai na QUERY (nao no body) e e' obrigatorio — e' o que
     * impede a task duplicar quando a conexao cai no meio e o cliente repete.
     * Use UUID v4 e REPITA a mesma chave no retry, nunca uma nova.
     *
     * @param  int  $endTime  epoch em SEGUNDOS; depois disso a task para de enviar
     * @param  list<string>  $productIds  cards de produto (limite vem do template)
     * @param  list<string>  $couponIds  cards de cupom (o tipo do cupom tem que bater com coupon_card_rules)
     */
    public function createTask(
        string $idempotencyKey,
        string $templateId,
        string $taskName,
        int $endTime,
        array $productIds = [],
        array $couponIds = [],
        string $channel = 'TIKTOK_IM',
        string $version = self::DEFAULT_VERSION,
    ): CreatedEngagementTaskResponseDTO {
        $body = [
            'template_id' => $templateId,
            'task_name' => $taskName,
            'end_time' => $endTime,
            'channel' => $channel,
        ];
        if ($productIds !== []) {
            $body['product_ids'] = $productIds;
        }
        if ($couponIds !== []) {
            $body['coupon_ids'] = $couponIds;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_engagement/{$version}/engagement_tasks",
            ['idempotency_key' => $idempotencyKey],
            $body,
        );

        return CreatedEngagementTaskResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cria a task com TEXTO PROPRIO (sem template).
     *
     * Exige a feature `CUSTOM_MSG` autorizada — cheque em getPermissions().
     * Limites do TikTok: titulo 1-70 caracteres, corpo 1-500, ate' 4 produtos
     * e 1 cupom.
     *
     * @param  list<string>  $productIds  max 4
     * @param  list<string>  $couponIds  max 1
     */
    public function createCustomTask(
        string $idempotencyKey,
        string $taskName,
        int $endTime,
        ?string $title = null,
        ?string $body = null,
        array $productIds = [],
        array $couponIds = [],
        string $channel = 'TIKTOK_IM',
        string $version = self::V202502,
    ): CreatedEngagementTaskResponseDTO {
        $payload = [
            'task_name' => $taskName,
            'end_time' => $endTime,
            'channel' => $channel,
        ];
        if ($productIds !== []) {
            $payload['product_ids'] = $productIds;
        }
        if ($couponIds !== []) {
            $payload['coupon_ids'] = $couponIds;
        }
        if ($title !== null || $body !== null) {
            $payload['custom_message'] = array_filter(
                ['title' => $title, 'body' => $body],
                static fn ($v) => $v !== null,
            );
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_engagement/{$version}/engagement_tasks/custom",
            ['idempotency_key' => $idempotencyKey],
            $payload,
        );

        return CreatedEngagementTaskResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Dispara a mensagem da task pros compradores.
     *
     * Duas armadilhas:
     * 1. O destinatario e' o e-mail ANONIMIZADO que vem do Get Order Detail
     *    (`...@scs.tiktokw.us`), nao o e-mail real do cliente.
     * 2. So' aceita quem comprou na loja nos ultimos 365 dias — os recusados
     *    voltam em `errors[]` com `code: 0` no envelope (sucesso parcial).
     *
     * @param  list<string>  $buyerEmails
     */
    public function sendMessage(
        array $buyerEmails,
        ?string $taskId = null,
        string $version = self::DEFAULT_VERSION,
    ): SendEngagementMessageResponseDTO {
        $body = ['buyer_emails' => $buyerEmails];
        if ($taskId !== null) {
            $body['task_id'] = $taskId;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_engagement/{$version}/messages",
            [],
            $body,
        );

        return SendEngagementMessageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Desempenho das tasks (enviadas, lidas, pedidos, GMV, cupons resgatados).
     *
     * @param  list<string>  $taskIds
     */
    public function getTaskPerformances(array $taskIds, string $version = self::DEFAULT_VERSION): EngagementTaskPerformancesResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/customer_engagement/{$version}/performances",
            [],
            ['task_ids' => $taskIds],
        );

        return EngagementTaskPerformancesResponseDTO::fromArray($response['data'] ?? []);
    }
}
