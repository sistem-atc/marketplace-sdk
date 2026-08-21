<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Reporting;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Ads\Bases\BaseMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Exceptions\AmazonAdsRequestException;

/**
 * Perfis + relatórios v3 da Amazon Ads API.
 *
 * O reporting v3 é ASSÍNCRONO: requestDailyCostReport() cria o relatório
 * (status PENDING/PROCESSING), getReport() consulta até COMPLETED e devolve a
 * `url` (S3 pré-assinada), downloadReportRows() baixa o GZIP_JSON e devolve
 * as linhas. Janela máxima ~31 dias por relatório; retenção ~95 dias.
 *
 * Produtos e reportTypeId: SPONSORED_PRODUCTS→spCampaigns,
 * SPONSORED_BRANDS→sbCampaigns, SPONSORED_DISPLAY→sdCampaigns. Colunas
 * mínimas pro gasto diário: ['date', 'cost'] com timeUnit DAILY e
 * groupBy ['campaign'] (o consumidor agrega por date).
 */
class ReportingMethods extends BaseMethods
{
    public const REPORT_TYPES = [
        'SPONSORED_PRODUCTS' => 'spCampaigns',
        'SPONSORED_BRANDS' => 'sbCampaigns',
        'SPONSORED_DISPLAY' => 'sdCampaigns',
    ];

    /**
     * Perfis de anunciante acessíveis pelo token (1 por marketplace).
     *
     * @return list<array<string, mixed>>  cada um com profileId, countryCode, accountInfo…
     *
     * @throws AmazonAdsRequestException
     */
    public function profiles(): array
    {
        $response = $this->http()->get($this->baseUrl().'/v2/profiles');

        $data = $this->decodeOrFail($response, '/v2/profiles');

        return array_values($data);
    }

    /**
     * Cria o relatório diário de custo. Devolve o reportId.
     *
     * @param  string  $adProduct  chave de self::REPORT_TYPES
     *
     * @throws AmazonAdsRequestException
     */
    public function requestDailyCostReport(string $profileId, string $adProduct, string $startDate, string $endDate): string
    {
        $reportTypeId = self::REPORT_TYPES[$adProduct] ?? null;

        if ($reportTypeId === null) {
            throw new AmazonAdsRequestException("adProduct desconhecido: {$adProduct}");
        }

        $response = $this->http($profileId)
            ->withHeaders(['Content-Type' => 'application/vnd.createasyncreportrequest.v3+json'])
            ->post($this->baseUrl().'/reporting/reports', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'configuration' => [
                    'adProduct' => $adProduct,
                    'groupBy' => ['campaign'],
                    'columns' => ['date', 'cost'],
                    'reportTypeId' => $reportTypeId,
                    'timeUnit' => 'DAILY',
                    'format' => 'GZIP_JSON',
                ],
            ]);

        $data = $this->decodeOrFail($response, 'POST /reporting/reports');

        $reportId = (string) ($data['reportId'] ?? '');

        if ($reportId === '') {
            throw new AmazonAdsRequestException('POST /reporting/reports sem reportId: '.json_encode($data));
        }

        return $reportId;
    }

    /**
     * Estado do relatório: {status: PENDING|PROCESSING|COMPLETED|FAILURE, url?}.
     *
     * @return array<string, mixed>
     *
     * @throws AmazonAdsRequestException
     */
    public function getReport(string $profileId, string $reportId): array
    {
        $response = $this->http($profileId)->get($this->baseUrl()."/reporting/reports/{$reportId}");

        return $this->decodeOrFail($response, "GET /reporting/reports/{$reportId}");
    }

    /**
     * Baixa e descompacta o GZIP_JSON da URL pré-assinada.
     *
     * @return list<array<string, mixed>>
     *
     * @throws AmazonAdsRequestException
     */
    public function downloadReportRows(string $url): array
    {
        $response = Http::withOptions(['decode_content' => false])->get($url);

        if (! $response->successful()) {
            throw new AmazonAdsRequestException('download do relatório falhou', $response->status());
        }

        $body = (string) $response->body();
        $json = @gzdecode($body);

        if ($json === false) {
            $json = $body; // alguns proxies já entregam descompactado
        }

        $rows = json_decode($json, true);

        if (! is_array($rows)) {
            throw new AmazonAdsRequestException('relatório baixado não é JSON válido');
        }

        return $rows;
    }
}
