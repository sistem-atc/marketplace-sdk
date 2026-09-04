<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Seo;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * SEO de produto/categoria (`/v1/seo/{id}`) — o id vem do campo `seo` do recurso.
 */
class SeoMethods extends BaseMethods
{
    /** @return array<string,mixed> */
    public function get(int|string $seoId): array
    {
        return $this->makeRequest(HttpMethod::GET, "seo/{$seoId}/");
    }

    /**
     * @param array<string,mixed> $data title, description
     * @return array<string,mixed>
     */
    public function update(int|string $seoId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "seo/{$seoId}/", [], $data);
    }
}
