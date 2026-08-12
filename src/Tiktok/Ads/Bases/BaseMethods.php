<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Ads\Bases;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;
use SistemAtc\Marketplaces\Tiktok\Ads\Exceptions\TiktokAdsRequestException;

/**
 * Base da MARKETING API do TikTok for Business (business-api.tiktok.com).
 *
 * Difere do TikTok Shop em tudo que importa: host próprio, autenticação por
 * header `Access-Token` (token de longa duração, sem assinatura HMAC nem
 * shop_cipher) e convenção de erro HTTP 200 + `code != 0` no corpo.
 *
 * Arrays em query vão como JSON string (`["a","b"]`), não campo repetido.
 */
abstract class BaseMethods
{
    public function __construct(protected MarketplaceIntegration $integration) {}

    /**
     * GET paginado — concatena `data.list` de todas as páginas.
     *
     * @return list<array<string, mixed>>
     *
     * @throws TiktokAdsRequestException
     */
    protected function paginatedList(string $apiPath, array $query, int $pageSize = 200): array
    {
        $rows = [];
        $page = 1;

        do {
            $data = $this->makeRequest($apiPath, $query + ['page' => $page, 'page_size' => $pageSize]);
            $rows = array_merge($rows, $data['list'] ?? []);

            $totalPages = (int) ($data['page_info']['total_page'] ?? 1);
            $page++;
        } while ($page <= $totalPages);

        return $rows;
    }

    /**
     * @return array<string, mixed> o `data` da resposta
     *
     * @throws TiktokAdsRequestException
     */
    protected function makeRequest(string $apiPath, array $query): array
    {
        $baseUrl = (string) config('marketplaces.tiktok_ads.base_url', 'https://business-api.tiktok.com');

        $response = Http::withHeaders(['Access-Token' => (string) $this->integration->getAccessToken()])
            ->acceptJson()
            ->get($baseUrl.$apiPath, $query)
            ->throw()
            ->json();

        if (($response['code'] ?? -1) !== 0) {
            throw new TiktokAdsRequestException(
                "TikTok Ads {$apiPath}: ".($response['message'] ?? 'erro desconhecido'),
                (int) ($response['code'] ?? 0),
            );
        }

        return $response['data'] ?? [];
    }
}
