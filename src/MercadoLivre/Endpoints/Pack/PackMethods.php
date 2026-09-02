<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Pack;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

/**
 * Packs (carrinho) do Mercado Livre: agrupam 1..N orders e 0..1 shipment.
 * Todo pedido novo nasce com pack_id; quando `orders.pack_id` vier null, use o
 * proprio order_id no lugar do pack_id (o recurso /packs aceita).
 *
 * Cobre tambem as notas informativas do pack (/packs/{id}/notes) e o anexo de
 * Nota Fiscal pro comprador (/packs/{id}/fiscal_documents).
 */
class PackMethods extends BaseMethods
{
    /**
     * Pack por id (GET /packs/{id}): `orders[].id`, `shipment.id`, `buyer.id`,
     * `status` (released|error|pending_cancel|cancelled) e `status_detail`.
     *
     * @return array<string, mixed>
     */
    public function get(int|string $packId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/packs/{$packId}");
    }

    /**
     * Notas informativas do pack (GET /packs/{id}/notes) — quem (seller,
     * colaborador) e de onde (postventa, ventas, integrador) anotou.
     *
     * GOTCHA: esta familia exige o header `X-Public: true` e a doc manda o
     * access_token tambem na QUERY (alem do Bearer); os dois vao juntos aqui.
     *
     * @return array<int|string, mixed>
     */
    public function notes(int|string $packId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/packs/{$packId}/notes",
            $this->tokenQuery(),
            headers: self::PUBLIC_HEADERS,
        );
    }

    /**
     * Uma nota informativa do pack (GET /packs/{id}/notes/{noteId}). O noteId
     * e alfanumerico (ObjectId), por isso vai url-encoded.
     *
     * @return array<string, mixed>
     */
    public function note(int|string $packId, int|string $noteId): array
    {
        $noteId = rawurlencode((string) $noteId);

        return $this->makeRequest(
            HttpMethod::GET,
            "/packs/{$packId}/notes/{$noteId}",
            $this->tokenQuery(),
            headers: self::PUBLIC_HEADERS,
        );
    }

    /**
     * Cria nota informativa no pack (POST /packs/{id}/notes, body {note}).
     * Max 300 caracteres. Resposta: `note.{id,date_created,note,seller_id,...}`.
     *
     * @return array<string, mixed>
     */
    public function createNote(int|string $packId, string $note): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/packs/{$packId}/notes",
            $this->tokenQuery(),
            ['note' => $note],
            headers: self::PUBLIC_HEADERS,
        );
    }

    /**
     * Atualiza o texto de uma nota informativa (PUT /packs/{id}/notes/{noteId}).
     *
     * @return array<string, mixed>
     */
    public function updateNote(int|string $packId, int|string $noteId, string $note): array
    {
        $noteId = rawurlencode((string) $noteId);

        return $this->makeRequest(
            HttpMethod::PUT,
            "/packs/{$packId}/notes/{$noteId}",
            $this->tokenQuery(),
            ['note' => $note],
            headers: self::PUBLIC_HEADERS,
        );
    }

    /**
     * Lista os documentos fiscais anexados ao pack (GET /packs/{id}/fiscal_documents):
     * `fiscal_documents[].{id,date,file_type,filename}`. Como vendedor so ve o
     * que ele mesmo subiu. 404 quando o pack nao tem nota anexada.
     *
     * @return array{pack_id?: int, fiscal_documents?: list<array<string, mixed>>}
     */
    public function fiscalDocuments(int|string $packId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/packs/{$packId}/fiscal_documents");
    }

    /**
     * Baixa o arquivo de um documento fiscal anexado
     * (GET /packs/{id}/fiscal_documents/{fiscalDocumentId}) — corpo bruto
     * (XML ou PDF). O id vem da listagem (ex.: `415460047_a96d8dea-...`).
     */
    public function downloadFiscalDocument(int|string $packId, string $fiscalDocumentId): string
    {
        $fiscalDocumentId = rawurlencode($fiscalDocumentId);
        $response = $this->httpClient->timeout(120)->get("/packs/{$packId}/fiscal_documents/{$fiscalDocumentId}");
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return (string) $response->body();
    }

    /**
     * Anexa Nota Fiscal ao pack pro comprador (POST /packs/{id}/fiscal_documents,
     * multipart, campo `fiscal_document` repetido). Dois modos: so XML (ML gera
     * o DANFE) ou PDF + XML (max 1 arquivo de cada tipo, 1 MB cada; XML precisa
     * comecar com `<?xml version="1.0" encoding="UTF-8"?>` antes do <nfeProc>).
     * Resposta: `{ids: [...]}` — guarde pra consultar/remover.
     *
     * ARMADILHAS: NAO altera status do envio nem libera etiqueta (pra isso use
     * InvoiceMethods::importShipmentInvoice). No MLB e proibido pra
     * fulfillment/cross_docking/xd_drop_off/drop_off (403) e pra quem usa o
     * Faturador ML (403 "must use the NF-e reporting flow").
     *
     * @param  list<array{contents: string, filename: string}>  $files  1 ou 2 arquivos (xml e/ou pdf)
     * @return array{ids?: list<string>}
     */
    public function attachFiscalDocuments(int|string $packId, array $files): array
    {
        if ($files === [] || count($files) > 2) {
            throw new \InvalidArgumentException('attachFiscalDocuments aceita 1 ou 2 arquivos (xml e/ou pdf).');
        }

        // attach() muta o PendingRequest -> clone pra nao vazar multipart pras
        // proximas chamadas do mesmo client.
        $client = clone $this->httpClient;
        foreach ($files as $file) {
            $client = $client->attach('fiscal_document', $file['contents'], $file['filename']);
        }

        $response = $client->post("/packs/{$packId}/fiscal_documents");
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return (array) $response->json();
    }

    /**
     * Remove TODOS os documentos fiscais que o vendedor anexou ao pack
     * (DELETE /packs/{id}/fiscal_documents). 404 se nao ha nada anexado.
     *
     * @return array{message?: string}
     */
    public function deleteFiscalDocuments(int|string $packId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/packs/{$packId}/fiscal_documents");
    }

    /** @var array<string, string> */
    private const PUBLIC_HEADERS = ['X-Public' => 'true'];

    /** @return array<string, string> */
    private function tokenQuery(): array
    {
        return ['access_token' => (string) $this->integration->getAccessToken()];
    }
}
