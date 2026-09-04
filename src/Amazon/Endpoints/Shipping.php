<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Shipping API v2 (path /shipping/v2). Cotação, compra, rastreio, documentos,
 * contas de transportadora, formulários de coleta, NDR e sinistros.
 *
 * Rate limit (modelo): 80 req/s + burst 100 em todas as operações, exceto
 * linkCarrierAccount via POST (5 req/s + burst 10).
 *
 * Headers opcionais do modelo:
 * - `x-amzn-shipping-business-id` (AmazonShipping_US, AmazonShipping_UK, ...):
 *   configure via {@see withBusinessId()} — devolve um CLONE imutável da
 *   instância que envia o header em TODAS as chamadas; a instância original
 *   segue sem o header.
 * - `x-amzn-IdempotencyKey`: parâmetro nomeado `$idempotencyKey` em
 *   {@see purchaseShipment()}, {@see directPurchaseShipment()} e
 *   {@see generateCollectionForm()}. Só é enviado quando informado.
 *
 * Respostas v2 vêm embrulhadas em `payload` (o array inteiro é devolvido).
 */
class Shipping
{
    private ?string $businessId = null;

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Clone imutável que envia `x-amzn-shipping-business-id: $businessId` em
     * todas as chamadas (ex.: AmazonShipping_US). A instância original não é
     * alterada.
     */
    public function withBusinessId(string $businessId): static
    {
        $clone = clone $this;
        $clone->businessId = $businessId;

        return $clone;
    }

    /**
     * Headers desta instância (+ idempotency key quando informada).
     *
     * @return array<string, string>
     */
    private function headers(?string $idempotencyKey = null): array
    {
        $headers = [];
        if ($this->businessId !== null) {
            $headers['x-amzn-shipping-business-id'] = $this->businessId;
        }
        if ($idempotencyKey !== null) {
            $headers['x-amzn-IdempotencyKey'] = $idempotencyKey;
        }

        return $headers;
    }

    /**
     * Cotação de serviços (POST /shipping/v2/shipments/rates). Retorno em
     * `payload.rates[]` + `payload.requestToken` (usado na compra).
     *
     * @param  array<string, mixed>  $body  GetRatesRequest (shipTo, shipFrom, packages, channelDetails, ...)
     */
    public function getRates(array $body): array
    {
        return $this->client->post('/shipping/v2/shipments/rates', $body, $this->headers());
    }

    /**
     * Compra direta de remessa sem cotação prévia
     * (POST /shipping/v2/shipments/directPurchase). Retorno em `payload`
     * (shipmentId, packageDocumentDetails).
     *
     * @param  array<string, mixed>  $body  DirectPurchaseRequest
     * @param  string|null  $idempotencyKey  Header `x-amzn-IdempotencyKey` (evita compra duplicada em retry)
     */
    public function directPurchaseShipment(array $body, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/shipping/v2/shipments/directPurchase', $body, $this->headers($idempotencyKey));
    }

    /**
     * Compra a remessa a partir de um rateId + requestToken
     * (POST /shipping/v2/shipments). Retorno em `payload`
     * (shipmentId, packageDocumentDetails, promise).
     *
     * @param  array<string, mixed>  $body  PurchaseShipmentRequest (requestToken, rateId, requestedDocumentSpecification, ...)
     * @param  string|null  $idempotencyKey  Header `x-amzn-IdempotencyKey` (evita compra duplicada em retry)
     */
    public function purchaseShipment(array $body, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/shipping/v2/shipments', $body, $this->headers($idempotencyKey));
    }

    /**
     * Cotação + compra numa chamada só (POST /shipping/v2/oneClickShipment).
     * Retorno em `payload` (shipmentId, packageDocumentDetails, carrier, service).
     *
     * @param  array<string, mixed>  $body  OneClickShipmentRequest
     */
    public function oneClickShipment(array $body): array
    {
        return $this->client->post('/shipping/v2/oneClickShipment', $body, $this->headers());
    }

    /**
     * Rastreio de um pacote (GET /shipping/v2/tracking). Retorno em `payload`
     * (trackingId, summary, eventHistory, promisedDeliveryDate).
     */
    public function getTracking(string $trackingId, string $carrierId): array
    {
        return $this->client->get('/shipping/v2/tracking', [
            'trackingId' => $trackingId,
            'carrierId' => $carrierId,
        ], $this->headers());
    }

    /**
     * Documentos (etiqueta etc.) de um pacote da remessa
     * (GET /shipping/v2/shipments/{shipmentId}/documents). Retorno em
     * `payload.packageDocumentDetail`.
     *
     * @param  array<string, mixed>  $query  format (PDF|PNG|ZPL), dpi
     */
    public function getShipmentDocuments(string $shipmentId, string $packageClientReferenceId, array $query = []): array
    {
        return $this->client->get(
            '/shipping/v2/shipments/'.rawurlencode($shipmentId).'/documents',
            array_merge(['packageClientReferenceId' => $packageClientReferenceId], $query),
            $this->headers(),
        );
    }

    /**
     * Cancela uma remessa comprada (PUT /shipping/v2/shipments/{shipmentId}/cancel).
     */
    public function cancelShipment(string $shipmentId): array
    {
        return $this->client->put('/shipping/v2/shipments/'.rawurlencode($shipmentId).'/cancel', [], $this->headers());
    }

    /**
     * Schema JSON dos inputs adicionais exigidos por um rate
     * (GET /shipping/v2/shipments/additionalInputs/schema). Retorno em `payload`.
     */
    public function getAdditionalInputs(string $requestToken, string $rateId): array
    {
        return $this->client->get('/shipping/v2/shipments/additionalInputs/schema', [
            'requestToken' => $requestToken,
            'rateId' => $rateId,
        ], $this->headers());
    }

    /**
     * Inputs necessários pra vincular uma conta de transportadora
     * (GET /shipping/v2/carrierAccountFormInputs). Retorno em
     * `payload.linkCarrierAccountInputs`.
     */
    public function getCarrierAccountFormInputs(): array
    {
        return $this->client->get('/shipping/v2/carrierAccountFormInputs', [], $this->headers());
    }

    /**
     * Contas de transportadora vinculadas (PUT /shipping/v2/carrierAccounts —
     * sim, PUT com body, é como o modelo define). Retorno em
     * `payload.activeCarrierAccounts`.
     *
     * @param  array<string, mixed>  $body  GetCarrierAccountsRequest (clientReferenceDetails)
     */
    public function getCarrierAccounts(array $body): array
    {
        return $this->client->put('/shipping/v2/carrierAccounts', $body, $this->headers());
    }

    /**
     * Vincula uma conta de transportadora ao seller
     * (PUT /shipping/v2/carrierAccounts/{carrierId}). Retorno em
     * `payload.registrationStatus`.
     *
     * O modelo expõe o mesmo operationId também via POST com rate limit
     * menor (5/10) — ver {@see linkCarrierAccountPost()}.
     *
     * @param  array<string, mixed>  $body  LinkCarrierAccountRequest
     */
    public function linkCarrierAccount(string $carrierId, array $body): array
    {
        return $this->client->put('/shipping/v2/carrierAccounts/'.rawurlencode($carrierId), $body, $this->headers());
    }

    /**
     * Variante POST de linkCarrierAccount
     * (POST /shipping/v2/carrierAccounts/{carrierId}). Rate limit 5 req/s +
     * burst 10. Mesmo body/retorno da variante PUT.
     *
     * @param  array<string, mixed>  $body  LinkCarrierAccountRequest
     */
    public function linkCarrierAccountPost(string $carrierId, array $body): array
    {
        return $this->client->post('/shipping/v2/carrierAccounts/'.rawurlencode($carrierId), $body, $this->headers());
    }

    /**
     * Desvincula uma conta de transportadora
     * (PUT /shipping/v2/carrierAccounts/{carrierId}/unlink). Retorno em
     * `payload.isUnlinked`.
     *
     * @param  array<string, mixed>  $body  UnlinkCarrierAccountRequest (clientReferenceDetails)
     */
    public function unlinkCarrierAccount(string $carrierId, array $body): array
    {
        return $this->client->put('/shipping/v2/carrierAccounts/'.rawurlencode($carrierId).'/unlink', $body, $this->headers());
    }

    /**
     * Gera formulário de coleta (manifesto) das remessas
     * (POST /shipping/v2/collectionForms). Retorno em `payload.collectionsFormDocument`.
     *
     * @param  array<string, mixed>  $body  GenerateCollectionFormRequest (carrierId, shipFromAddress, ...)
     * @param  string|null  $idempotencyKey  Header `x-amzn-IdempotencyKey` (evita compra duplicada em retry)
     */
    public function generateCollectionForm(array $body, ?string $idempotencyKey = null): array
    {
        return $this->client->post('/shipping/v2/collectionForms', $body, $this->headers($idempotencyKey));
    }

    /**
     * Histórico de formulários de coleta (PUT /shipping/v2/collectionForms/history).
     * Retorno em `payload.collectionFormsHistoryRecordList`.
     *
     * @param  array<string, mixed>  $body  GetCollectionFormHistoryRequest (carrierId, shipFromAddress, maxResults, ...)
     */
    public function getCollectionFormHistory(array $body): array
    {
        return $this->client->put('/shipping/v2/collectionForms/history', $body, $this->headers());
    }

    /**
     * Remessas ainda não manifestadas (PUT /shipping/v2/unmanifestedShipments).
     * Retorno em `payload.unmanifestedCarrierInformationList`.
     *
     * @param  array<string, mixed>  $body  GetUnmanifestedShipmentsRequest (clientReferenceDetails)
     */
    public function getUnmanifestedShipments(array $body): array
    {
        return $this->client->put('/shipping/v2/unmanifestedShipments', $body, $this->headers());
    }

    /**
     * Reobtém um formulário de coleta
     * (GET /shipping/v2/collectionForms/{collectionFormId}). Retorno em
     * `payload.collectionsFormDocument`.
     */
    public function getCollectionForm(string $collectionFormId): array
    {
        return $this->client->get('/shipping/v2/collectionForms/'.rawurlencode($collectionFormId), [], $this->headers());
    }

    /**
     * Pontos de acesso (lockers/agências) próximos (GET /shipping/v2/accessPoints).
     * Retorno em `payload.accessPointsMap`.
     *
     * @param  list<string>  $accessPointTypes  ex.: ['HELIX', 'COUNTER']
     */
    public function getAccessPoints(array $accessPointTypes, string $countryCode, string $postalCode): array
    {
        return $this->client->get('/shipping/v2/accessPoints', [
            'accessPointTypes' => implode(',', $accessPointTypes),
            'countryCode' => $countryCode,
            'postalCode' => $postalCode,
        ], $this->headers());
    }

    /**
     * Feedback pra Non-Delivery Report (POST /shipping/v2/ndrFeedback).
     *
     * @param  array<string, mixed>  $body  SubmitNdrFeedbackRequest (trackingId, ndrRequestData)
     */
    public function submitNdrFeedback(array $body): array
    {
        return $this->client->post('/shipping/v2/ndrFeedback', $body, $this->headers());
    }

    /**
     * Abre sinistro (extravio/dano) (POST /shipping/v2/claims). Retorno em
     * `payload.claimId`.
     *
     * @param  array<string, mixed>  $body  CreateClaimRequest (trackingId, claimType, amount, ...)
     */
    public function createClaim(array $body): array
    {
        return $this->client->post('/shipping/v2/claims', $body, $this->headers());
    }
}
