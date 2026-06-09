<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Reverse;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ReverseMethods extends BaseMethods
{
    public function searchReturns(array $filters = [], int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/reverse/v202309/returns/search', [], array_merge([
            'page_size' => $pageSize,
        ], $filters));
    }

    public function getReturnDetail(string $reverseOrderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/api/reverse/v202309/returns/{$reverseOrderId}");
    }
}
