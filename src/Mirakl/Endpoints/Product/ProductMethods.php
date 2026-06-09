<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Endpoints\Product;

use SistemAtc\Marketplaces\Mirakl\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ProductMethods extends BaseMethods
{
    public function listOffers(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'offers', $params);
    }

    public function updateOffers(array $offers): array
    {
        return $this->makeRequest(HttpMethod::POST, 'offers', [], ['offers' => $offers]);
    }

    public function getOffer(string $offerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "offers/{$offerId}");
    }
}
