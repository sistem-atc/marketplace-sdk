<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Payment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Formas de pagamento da loja (`/v1/pagamento`). Paginação `limit/offset`.
 */
class PaymentMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'pagamento/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "pagamento/{$id}/");
    }
}
