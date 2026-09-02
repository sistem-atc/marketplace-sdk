<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment\ShipmentHistoryEvent;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Shipment\ShipmentResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class ShipmentMethods extends BaseMethods
{
    /**
     * Envio completo (GET /shipments/{id}) como DTO tipado — ponto UNICO de parse.
     * Arvore tipada (status/substatus, logistic_type, tracking, receiver_address,
     * shipping_option, status_history); `toArray()` e' LOSSLESS (validado contra
     * 600 envios reais), entao serve pra acesso tipado E pra gravar o raw.
     *
     * 404 -> null (envio inexistente), como antes.
     */
    public function getShipment(int|string $shippingId): ?ShipmentResponseDTO
    {
        try {
            return ShipmentResponseDTO::fromArray(
                $this->makeRequest(HttpMethod::GET, "/shipments/{$shippingId}")
            );
        } catch (MercadoLivreRequestException $e) {
            if ($e->status() === 404) return null;
            throw $e;
        }
    }

    public function downloadLabels(array $shippingIds, string $responseType = 'pdf'): string
    {
        $query = ['shipment_ids' => implode(',', $shippingIds), 'response_type' => $responseType];
        $response = $this->httpClient->get('/shipment_labels', $query);
        if (!$response->successful()) throw new MercadoLivreRequestException($response);
        return $response->body();
    }

    /**
     * Historico de transicoes do envio como DTOs tipados.
     *
     * @return list<ShipmentHistoryEvent>
     */
    public function history(int|string $shipmentId): array
    {
        $response = $this->httpClient->withHeaders(['x-format-new' => 'true'])->get("/shipments/{$shipmentId}/history");
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        $events = $response->json()['history'] ?? $response->json();

        return array_map(
            fn (array $e) => ShipmentHistoryEvent::fromArray($e),
            (array) $events,
        );
    }

    // ------------------------------------------------------------------
    // Sub-recursos de /shipments/{id} (Gerenciamento de envios)
    // ------------------------------------------------------------------

    /**
     * Itens do envio (GET /shipments/{id}/items) — item_id, quantity, variation_id,
     * dimensions. Exige header `x-format-new: true`. O vendedor so ve os proprios
     * produtos; em carrinho (pack) vem 1 linha por item/variacao.
     *
     * @return array<int, array<string,mixed>>
     */
    public function items(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/items",
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Custos do envio (GET /shipments/{id}/costs): gross_amount + rateio
     * receiver/senders (cost, compensation, discounts). Exige `x-format-new: true`.
     * Campo `save` foi zerado em out/2024 e removido em jan/2025 — nao dependa dele.
     * Diferente de payments(), NAO exige pack_id.
     *
     * @return array<string,mixed>
     */
    public function costs(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/costs",
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Pagamentos do frete (GET /shipments/{id}/payments): payment_id, user_id,
     * amount, status. Exige `x-format-new: true` e que o envio esteja associado
     * a um pack_id (senao 404). Conferir status=approved antes de despachar.
     *
     * @return array<int, array<string,mixed>>
     */
    public function payments(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/payments",
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Prazo maximo de despacho (GET /shipments/{id}/sla): status (on_time/delayed),
     * service (xd_same_day, instant_gm = Envios Agora...), expected_date, last_updated.
     * Nao consultar para envios cancelados nem Fulfillment (sem dado). O horario
     * pode mudar ate o dia anterior — revalidar no dia do despacho.
     *
     * @return array<string,mixed>
     */
    public function sla(int|string $shipmentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shipments/{$shipmentId}/sla");
    }

    /**
     * Atrasos do envio (GET /shipments/{id}/delays): lista de {type, date, source};
     * type=sla_delayed = passou do SLA, handling_delayed = manuseio. Exige
     * `x-format-new: true`. Quando NAO ha atraso a API devolve 404 ("Delays Not
     * Found") — aqui vira lista vazia em vez de excecao.
     *
     * @return array<string,mixed>
     */
    public function delays(int|string $shipmentId): array
    {
        try {
            return $this->makeRequest(
                HttpMethod::GET,
                "/shipments/{$shipmentId}/delays",
                headers: ['x-format-new' => 'true'],
            );
        } catch (MercadoLivreRequestException $e) {
            if ($e->status() === 404) return ['shipment_id' => $shipmentId, 'delays' => []];
            throw $e;
        }
    }

    /**
     * Prazos de entrega (GET /shipments/{id}/lead_time): shipping_method, cost,
     * estimated_delivery_time/limit/final, estimated_handling_limit, delivery_promise.
     * Exige `x-format-new: true`.
     *
     * @return array<string,mixed>
     */
    public function leadTime(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/lead_time",
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Transportadora do envio (GET /shipments/{id}/carrier): {name, url} do
     * rastreio externo. Exige `x-format-new: true`.
     *
     * @return array<string,mixed>
     */
    public function carrier(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/carrier",
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Pedidos contidos no envio (GET /shipments/{id}/orders) — util em carrinho,
     * onde 1 shipment agrupa N orders. Exige header `X-New-Domain: true`
     * (obrigatorio neste recurso, sem ele 4xx).
     *
     * @return array<int, array<string,mixed>>
     */
    public function orders(int|string $shipmentId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/shipments/{$shipmentId}/orders",
            headers: ['X-New-Domain' => 'true'],
        );
    }

    /**
     * Catalogo de status/substatus possiveis de um envio (GET /shipment_statuses):
     * lista de {id, name, substatuses[]}. Exige `x-format-new: true`. Estatico —
     * cachear.
     *
     * @return array<int, array<string,mixed>>
     */
    public function statuses(): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/shipment_statuses',
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Divide um envio de carrinho em varios pacotes (POST /shipments/{id}/split).
     * Body: {reason: OTHER_MOTIVE|DIMENSIONS_EXCEEDED, packs: [{package_id?, orders: [{id, quantity}]}]}.
     * Exige `x-format-new: true`. So funciona antes da impressao da etiqueta.
     *
     * @param  array<int, array<string,mixed>>  $packs
     * @return array<string,mixed>
     */
    public function split(int|string $shipmentId, string $reason, array $packs): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/shipments/{$shipmentId}/split",
            body: ['reason' => $reason, 'packs' => $packs],
            headers: ['x-format-new' => 'true'],
        );
    }

    /**
     * Marca "ja tenho o produto" / estoque disponivel (POST /shipments/{id}/process/ready_to_ship)
     * — libera o envio pra despacho quando o pack estava aguardando confirmacao
     * do vendedor. Sem body; resposta {status: 200}. 403 se o envio nao permite.
     *
     * @return array<string,mixed>
     */
    public function markReadyToShip(int|string $shipmentId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/shipments/{$shipmentId}/process/ready_to_ship");
    }

    /**
     * Notificacao de status pelo vendedor em envios ME1 / frete proprio
     * (POST /v2/shipments/{id}/seller_notifications). A V1 (/shipments/{id}/seller_notifications)
     * foi descontinuada em 31/10 — so a V2 existe aqui.
     *
     * Body obrigatorio: payload.service_id (MLB = 11), payload.date (ISO-8601 com
     * timezone), status (shipped|delivered|not_delivered) e substatus (SEMPRE
     * presente, `null` quando nao houver — nunca omitir). tracking_number e
     * tracking_url sao opcionais mas andam JUNTOS (um sem o outro = 400).
     * payload.comment e texto livre opcional.
     *
     * @param  array<string,mixed>  $payload  {service_id, date, comment?}
     * @return array<string,mixed>  {status: "OK"}
     */
    public function sellerNotification(
        int|string $shipmentId,
        string $status,
        ?string $substatus,
        array $payload,
        ?string $trackingNumber = null,
        ?string $trackingUrl = null,
    ): array {
        $body = [
            'payload' => $payload,
            'status' => $status,
            'substatus' => $substatus,
        ];
        if ($trackingNumber !== null || $trackingUrl !== null) {
            $body['tracking_number'] = $trackingNumber;
            $body['tracking_url'] = $trackingUrl;
        }

        return $this->makeRequest(HttpMethod::POST, "/v2/shipments/{$shipmentId}/seller_notifications", body: $body);
    }


    /**
     * Atualiza um envio PERSONALIZADO (shipping mode `custom`, fora do
     * Mercado Envios) — PUT /shipments/{id}. Body: `receiver_id` (comprador,
     * sempre) + `tracking_number`, `status` (shipped|delivered|cancelled),
     * `speed` (horas até entregar; vira a promessa de entrega) e `comments`.
     * Pending→shipped exige tracking_number; delivered/cancelled só o
     * receiver_id. Não serve pra envios do ME — esses são read-only.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function updateCustomShipment(int|string $shipmentId, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/shipments/{$shipmentId}", [], $body);
    }
}
