<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Shipment;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class ShipmentMethods extends BaseMethods
{
    public function getShipment(int|string $shippingId): ?array
    {
        try {
            return $this->makeRequest(HttpMethod::GET, "/shipments/{$shippingId}");
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

    public function history(int|string $shipmentId): array
    {
        $response = $this->httpClient->withHeaders(['x-format-new' => 'true'])->get("/shipments/{$shipmentId}/history");
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return $response->json()['history'] ?? $response->json();
    }
}
