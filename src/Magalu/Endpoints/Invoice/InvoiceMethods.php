<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Invoice;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class InvoiceMethods extends BaseMethods
{
    /** Range max documentado pela Magalu por chamada. */
    private const MAX_RANGE_DAYS = 30;

    public function getFulfillmentSignedUrl(string $startDate, string $endDate): array
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

        return $this->makeRequest(
            HttpMethod::GET,
            '/seller/v1/invoices/fulfillment',
            ['start_date' => $startDate, 'end_date' => $endDate]
        );
    }

    public function downloadFulfillmentZip(string $signedUrl): string
    {
        $response = Http::timeout(180)->get($signedUrl);
        if ($response->failed()) throw new MagaluRequestException($response);
        return $response->body();
    }

    public function listForDelivery(string $deliveryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/invoices");
    }

    private function assertValidDate(string $date, string $field): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException("{$field} invalido: {$date}. Use YYYY-MM-DD.");
        }
    }
}
