<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Invoice;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Invoice\DeliveryInvoice;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Invoice\FulfillmentSignedUrl;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class InvoiceMethods extends BaseMethods
{
    /** Range max documentado pela Magalu por chamada. */
    private const MAX_RANGE_DAYS = 30;

    public function create(string $orderId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/orders/{$orderId}/invoices", [], $data);
    }

    public function getFulfillmentSignedUrl(string $startDate, string $endDate): FulfillmentSignedUrl
    {
        $this->assertValidDate($startDate, 'startDate');
        $this->assertValidDate($endDate, 'endDate');

        $start = strtotime($startDate);
        $end = strtotime($endDate);

        if ($end < $start) {
            throw new InvalidArgumentException("endDate ({$endDate}) anterior a startDate ({$startDate}).");
        }

        $diffDays = ($end - $start) / 86400;
        if ($diffDays > self::MAX_RANGE_DAYS) {
            throw new InvalidArgumentException(sprintf(
                'Range de %d dias excede o max de %d dias (Magalu API). Fragmente em janelas menores.',
                (int) $diffDays,
                self::MAX_RANGE_DAYS,
            ));
        }

        return FulfillmentSignedUrl::fromArray($this->makeRequest(
            HttpMethod::GET,
            '/seller/v1/invoices/fulfillment',
            ['start_date' => $startDate, 'end_date' => $endDate]
        ));
    }

    public function downloadFulfillmentZip(string $signedUrl): string
    {
        $response = Http::timeout(180)->get($signedUrl);
        if ($response->failed()) throw new MagaluRequestException($response);
        return $response->body();
    }

    /** @return list<DeliveryInvoice> */
    public function listForDelivery(string $deliveryId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/invoices");

        return array_map(
            static fn (array $i): DeliveryInvoice => DeliveryInvoice::fromArray($i),
            $response['results'] ?? [],
        );
    }

    private function assertValidDate(string $date, string $field): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException("{$field} invalido: {$date}. Use YYYY-MM-DD.");
        }
    }
}
