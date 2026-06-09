<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Claim;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ClaimMethods extends BaseMethods
{
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/claims', $params);
    }

    public function get(string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/claims/{$claimId}");
    }

    public function reply(string $claimId, string $message): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/claims/{$claimId}/messages", [], [
            'message' => $message,
        ]);
    }
}
