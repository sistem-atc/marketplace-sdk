<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Delivery Shipment Invoicing API 2022-07-01 (Delivery by Amazon / DBA) —
 * envio e status da nota fiscal (NF-e) de remessa pro marketplace BRASIL,
 * pros programas EasyShip (DBA), FbaOnSite e SelfShip.
 *
 * Diferente do Shipment Invoicing v0 (`Invoices`), aqui o `orderId` OU
 * `shipmentId` vai na QUERY (orderId prevalece se os dois vierem) e a nota
 * vai no body em base64. Rate limit das duas operações: 1.133 req/s + burst 25.
 */
class DeliveryByAmazon
{
    use FlattensCsvQuery;

    private const BASE = '/delivery/2022-07-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Envia a NF-e de uma remessa (POST /delivery/2022-07-01/invoice?orderId=|shipmentId=).
     * Body SubmitInvoiceRequest: `invoiceContent` (XML em base64),
     * `contentMD5Value` (MD5 base64 do XML), `invoiceType` (Outbound),
     * `programType` (EasyShip|FbaOnSite|SelfShip), `marketplaceId`.
     * Resposta só com `errors[]` (vazio = aceita). Informe `orderId` ou
     * `shipmentId` — um dos dois é obrigatório.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function submitInvoice(array $body, ?string $orderId = null, ?string $shipmentId = null): array
    {
        $query = $this->csv(['orderId' => $orderId, 'shipmentId' => $shipmentId]);

        return $this->client->post(self::BASE.'/invoice?'.http_build_query($query), $body);
    }

    /**
     * Status da NF-e de uma remessa (GET /delivery/2022-07-01/invoice/status).
     * `invoiceType`: Outbound; `programType`: EasyShip|FbaOnSite|SelfShip.
     * Informe `orderId` ou `shipmentId`. Retorna na raiz `amazonOrderId`,
     * `amazonShipmentId`, `invoiceStatus` (Processing|Accepted|Errored|NotFound).
     *
     * @return array<string, mixed>
     */
    public function getInvoiceStatus(string $marketplaceId, string $invoiceType, string $programType, ?string $orderId = null, ?string $shipmentId = null): array
    {
        return $this->client->get(self::BASE.'/invoice/status', $this->csv([
            'orderId' => $orderId,
            'shipmentId' => $shipmentId,
            'marketplaceId' => $marketplaceId,
            'invoiceType' => $invoiceType,
            'programType' => $programType,
        ]));
    }
}
