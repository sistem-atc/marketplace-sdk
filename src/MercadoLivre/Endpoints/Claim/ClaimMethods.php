<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Claim;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class ClaimMethods extends BaseMethods
{
    /**
     * Busca detalhes de uma reclamacao.
     */
    public function get(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}");
    }

    /**
     * Pesquisa reclamacoes com filtros.
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/claims/search', $filters);
    }

    /**
     * Busca o historico de eventos de uma reclamacao.
     */
    public function history(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}/history");
    }

    /**
     * Busca as mensagens trocadas dentro da reclamacao.
     */
    public function messages(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/claims/{$claimId}/messages");
    }

    /**
     * Envia uma mensagem na reclamacao.
     */
    public function sendMessage(int|string $claimId, int|string $senderId, string $message, string $role = 'seller'): array
    {
        return $this->makeRequest(HttpMethod::POST, "/claims/{$claimId}/messages", [], [
            'sender_id' => $senderId,
            'role' => $role,
            'message' => $message
        ]);
    }

    // ── API nova: /post-purchase/v1/claims ────────────────────────────────
    // Os metodos acima (/claims/*) sao o legado. Tudo abaixo segue a doc
    // "Reclamacoes, Devolucoes e Trocas" e devolve array cru.

    private const V1 = '/post-purchase/v1/claims';

    /**
     * Busca reclamacoes (API nova). Exige PELO MENOS UM filtro real — offset,
     * limit e sort sozinhos dao 400 `invalid_query`. Filtros: id, type, stage,
     * status, resource(+resource_id), reason_id, site_id, players.role
     * (+players.user_id), order_id OU pack_id (excludentes), payment_id,
     * parent_id, date_created/last_updated (via `range=campo:after:x,before:y`
     * com milissegundos). offset+limit < 10000; limit max 100 (default 30).
     * Resposta: `{paging, data: [...]}`.
     *
     * @param  array<string,mixed>  $filters
     */
    public function searchV1(array $filters): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1.'/search', $filters);
    }

    /** Reclamacao pela API nova — traz players[].available_actions e stage. */
    public function getV1(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}");
    }

    /** Detalhe expandido (item, envio, pagamento e resolucoes) da reclamacao. */
    public function detail(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/detail");
    }

    /**
     * Motivo (reason) de uma reclamacao, ex.: PDD9939. Query opcional: flow,
     * delivered (true/false), deep (arvore de dependencias) e name.
     *
     * @param  array<string,mixed>  $query
     */
    public function reason(string $reasonId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1.'/reasons/'.rawurlencode($reasonId), $query);
    }

    /** Historico de acoes executadas pelos players da reclamacao. */
    public function actionsHistory(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/actions-history");
    }

    /** Historico de mudancas de status/stage da reclamacao. */
    public function statusHistory(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/status-history");
    }

    /** Indica se a reclamacao afeta (ou afetou) a reputacao do vendedor. */
    public function affectsReputation(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/affects-reputation");
    }

    /** Mensagens da reclamacao pela API nova (com receiver_role e attachments). */
    public function messagesV1(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/messages");
    }

    /**
     * Sobe um arquivo pra ser anexado depois numa mensagem (multipart `file`).
     * Nome do arquivo: ate 125 chars, so [a-zA-Z0-9_\-.]. Devolve
     * `{user_id, filename}` — o `filename` e o que vai em `attachments[]` do
     * sendMessageV1().
     */
    public function uploadAttachment(int|string $claimId, string $contents, string $filename): array
    {
        $response = (clone $this->httpClient)
            ->attach('file', $contents, $filename)
            ->post(self::V1."/{$claimId}/attachments");

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return $response->json() ?? [];
    }

    /** Metadados de um anexo de mensagem (filename, size, type...). */
    public function attachmentInfo(int|string $claimId, string $attachmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/attachments/".rawurlencode($attachmentId));
    }

    /** Binario de um anexo de mensagem da reclamacao. */
    public function downloadAttachment(int|string $claimId, string $attachmentId): string
    {
        $response = $this->httpClient->get(self::V1."/{$claimId}/attachments/".rawurlencode($attachmentId).'/download');

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return (string) $response->body();
    }

    /**
     * Envia mensagem na reclamacao (API nova). So funciona se o player tiver a
     * acao send_message_to_* em available_actions. `receiverRole`:
     * complainant | respondent | mediator (mediator so na etapa dispute).
     * Resposta e 201 sem corpo util.
     *
     * @param  list<string>  $attachments  filenames devolvidos por uploadAttachment()
     */
    public function sendMessageV1(int|string $claimId, string $message, string $receiverRole = 'complainant', array $attachments = []): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/actions/send-message", [], [
            'receiver_role' => $receiverRole,
            'message' => $message,
            'attachments' => array_values($attachments),
        ]);
    }

    /** Evidencias ja carregadas na reclamacao. */
    public function evidences(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/evidences");
    }

    /**
     * Promessa de envio (produto ainda nao despachado). Body padrao
     * `{type: handling_shipping_evidence, handling_date: YYYY-MM-DD}`; passe
     * $evidence completo pra outros tipos aceitos em POST /evidences.
     *
     * @param  array<string,mixed>  $evidence
     */
    public function addEvidence(int|string $claimId, array $evidence): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/evidences", [], $evidence);
    }

    /**
     * Carrega evidencia de envio/entrega (POST /actions/evidences). Tipos:
     * shipping_evidence, delivery_evidence, etc. — campos obrigatorios variam
     * por tipo (shipping_method, shipping_company_name, tracking_number,
     * date_shipped, receiver_name/id...). `attachments` recebe os file_name
     * de uploadEvidenceAttachment().
     *
     * @param  array<string,mixed>  $evidence
     */
    public function submitEvidence(int|string $claimId, array $evidence): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/actions/evidences", [], $evidence);
    }

    /**
     * Sobe arquivo de evidencia (multipart `file`, JPG/PNG/PDF ate 5 MB).
     * Devolve `{user_id, file_name}` — file_name e o $attachmentId dos
     * metodos abaixo. `$public=true` manda o header `x-public: true` do
     * exemplo oficial.
     */
    public function uploadEvidenceAttachment(int|string $claimId, string $contents, string $filename, bool $public = false): array
    {
        $client = (clone $this->httpClient)->attach('file', $contents, $filename);
        if ($public) {
            $client = $client->withHeaders(['x-public' => 'true']);
        }
        $response = $client->post(self::V1."/{$claimId}/attachments-evidences");

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return $response->json() ?? [];
    }

    /** Metadados de um anexo de evidencia (filename, original_filename, size, type). */
    public function evidenceAttachmentInfo(int|string $claimId, string $attachmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/attachments-evidences/".rawurlencode($attachmentId));
    }

    /** Binario de um anexo de evidencia. */
    public function downloadEvidenceAttachment(int|string $claimId, string $attachmentId): string
    {
        $response = $this->httpClient->get(self::V1."/{$claimId}/attachments-evidences/".rawurlencode($attachmentId).'/download');

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return (string) $response->body();
    }

    /** Resolucoes esperadas por cada player (return_product, refund, partial_refund...). */
    public function expectedResolutions(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/expected-resolutions");
    }

    /**
     * Ofertas possiveis de reembolso parcial: `{currency_id, available_offers:
     * [{amount, percentage}], recommendations, restrictions}`. Sem percentual
     * escolhido o ML aplica default de 50%.
     */
    public function partialRefundAvailableOffers(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::V1."/{$claimId}/partial-refund/available-offers");
    }

    /**
     * Oferece reembolso parcial ao comprador. NAO aceita 100% (use refund()).
     * Use um percentage listado em partialRefundAvailableOffers().
     */
    public function offerPartialRefund(int|string $claimId, float|int $percentage): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/expected-resolutions/partial-refund", [], [
            'percentage' => $percentage,
        ]);
    }

    /** Devolucao TOTAL do dinheiro (exige available_action `refund`). */
    public function refund(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/expected-resolutions/refund");
    }

    /** Aceita a devolucao do produto (substitui aceitar/carregar resolucao). */
    public function allowReturn(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/expected-resolutions/allow-return");
    }

    /** Pede mediacao do ML — a reclamacao passa pra stage `dispute`. */
    public function openDispute(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::POST, self::V1."/{$claimId}/actions/open-dispute");
    }
}
