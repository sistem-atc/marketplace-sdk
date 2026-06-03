<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Logistics;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;

class LogisticsMethods extends BaseMethods
{
    public function generateShippingLabels(array $deliveryIds): string
    {
        $body = ['deliveries' => array_map(fn (string $id) => ['id' => $id], array_values($deliveryIds))];
        $response = $this->httpClient->post('/seller/v1/logistics/shipping-labels', $body);
        if (!$response->successful()) throw new MagaluRequestException($response);
        return $response->body();
    }
}
