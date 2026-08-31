<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Analytics;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\BestsellingCreator;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\BestsellingLiveSession;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\BestsellingProduct;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\BestsellingVideo;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomCoreStats;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomGmvTrendPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomProductStat;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomTrafficPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomUserPortraits;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\LiveRoomValueTrendPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopHourlyPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopLiveMinutePerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopLiveOverviewPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopLiveProductPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopLiveSessionPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopProductPerformanceDetail;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopProductPerformanceSummary;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopSkuPerformanceDetail;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopSkuPerformanceSummary;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopVideoOverviewPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopVideoPerformanceDetail;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopVideoPerformanceSummary;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\ShopVideoProductPerformance;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\SpsMetric;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\SpsMetricDiagnosis;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\SpsMetricProblemDetails;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\SpsMetricTopItems;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\SpsOverview;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Analytics\VideoPerformance;

/**
 * Analytics API do TikTok Shop — relatorios de desempenho da LOJA (GMV, trafego,
 * conversao) e da AUDIENCIA (lives, videos, creators).
 *
 * NAO confundir com `Tiktok::ads()`: aquilo e' a Marketing API
 * (business-api.tiktok.com, gasto de anuncio, outro host e outro token). Tudo
 * aqui roda no host normal do Shop, com assinatura HMAC + shop_cipher.
 *
 * TRES COISAS QUE VALEM PRA TODO ENDPOINT DESTE GRUPO:
 *
 * 1. NUMERO MONETARIO E STRING (`{"amount": "39440.00", "currency": "BRL"}`), e
 *    TAXA TAMBEM ("0.0528"). Nao tipamos float em lugar nenhum — comeria a casa
 *    decimal e quebraria o roundtrip. Converta no consumidor.
 * 2. AS JANELAS NAO SAO IGUAIS: `start_date_ge` e' inclusivo e `end_date_lt`
 *    EXCLUSIVO, ambos ISO YYYY-MM-DD no fuso REGISTRADO DA LOJA — nao em UTC.
 *    Ja' os endpoints de creator (`live_rooms`, `videos/performances`) usam epoch
 *    em SEGUNDOS.
 * 3. `latest_available_date` diz ate' onde o dado FECHOU. O pipeline do TikTok
 *    tem ~1 dia de atraso: pedir alem disso devolve zero, nao erro.
 *
 * Escopos exigidos variam por familia: `data.bestselling.public.read` (rankings),
 * `creator.data.live.read.public` (live_rooms) e `data.shop_analytics.public.read`
 * (loja/SPS).
 */
class AnalyticsMethods extends BaseMethods
{
    // ------------------------------------------------------------------
    // Bestsellers — ranking PUBLICO da plataforma, nao da sua loja.
    // GMV nunca vem exato aqui, so' em faixa ("$97K ~ $191K").
    // ------------------------------------------------------------------

    /**
     * Creators mais vendedores. `time_slot` (1D/7D/30D) define a janela que
     * termina em `date` — os dois sao obrigatorios, assim como `author_type`
     * (OFFICIAL | CHANNEL | AFFILIATE).
     *
     * @param  array<string, mixed>  $filters
     * @return array{creators: list<BestsellingCreator>}
     */
    public function getBestsellingCreators(array $filters = [], string $version = '202511'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/creators/bestselling", $filters);

        return ['creators' => array_map(
            static fn (array $c): BestsellingCreator => BestsellingCreator::fromArray($c),
            $response['data']['creators'] ?? [],
        )];
    }

    /**
     * Sessoes de LIVE mais vendedoras da plataforma.
     *
     * @param  array<string, mixed>  $filters
     * @return array{lives: list<BestsellingLiveSession>}
     */
    public function getBestsellingLiveSessions(array $filters = [], string $version = '202511'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/lives/bestselling", $filters);

        return ['lives' => array_map(
            static fn (array $l): BestsellingLiveSession => BestsellingLiveSession::fromArray($l),
            $response['data']['lives'] ?? [],
        )];
    }

    /**
     * Produtos mais vendidos. `category_id` aceita SO categoria L1.
     *
     * @param  array<string, mixed>  $filters
     * @return array{products: list<BestsellingProduct>}
     */
    public function getBestsellingProducts(array $filters = [], string $version = '202511'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/products/bestselling", $filters);

        return ['products' => array_map(
            static fn (array $p): BestsellingProduct => BestsellingProduct::fromArray($p),
            $response['data']['products'] ?? [],
        )];
    }

    /**
     * Videos mais vendedores. As metricas de engajamento vem ACUMULADAS desde a
     * publicacao, nao restritas a' janela consultada.
     *
     * @param  array<string, mixed>  $filters
     * @return array{videos: list<BestsellingVideo>}
     */
    public function getBestsellingVideos(array $filters = [], string $version = '202511'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/videos/bestselling", $filters);

        return ['videos' => array_map(
            static fn (array $v): BestsellingVideo => BestsellingVideo::fromArray($v),
            $response['data']['videos'] ?? [],
        )];
    }

    // ------------------------------------------------------------------
    // Live rooms — visao de CREATOR sobre UMA transmissao (escopo
    // creator.data.live.read.public). O id vai no PATH, nao na query.
    // ------------------------------------------------------------------

    /** Consolidado de uma transmissao (GMV, pedidos, audiencia, engajamento). */
    public function getLiveRoomCoreStats(string $liveRoomId, string $version = '202502'): ?LiveRoomCoreStats
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/core_stats',
        );

        $stats = $response['data']['stats'] ?? null;

        return is_array($stats) ? LiveRoomCoreStats::fromArray($stats) : null;
    }

    /**
     * Serie de GMV/pedidos criados ao longo da transmissao. Cada serie diz em
     * `statsType` o que mede (TREND_GMV ou TREND_CREATED_ORDER) — o data point
     * so' preenche o campo correspondente.
     *
     * @return array{gmv_trend_performances: list<LiveRoomGmvTrendPerformance>}
     */
    public function getLiveRoomGmvTrend(string $liveRoomId, string $version = '202502'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/gmv_trend_performances',
        );

        return ['gmv_trend_performances' => array_map(
            static fn (array $p): LiveRoomGmvTrendPerformance => LiveRoomGmvTrendPerformance::fromArray($p),
            $response['data']['gmv_trend_performances'] ?? [],
        )];
    }

    /**
     * Series de interacao da live (WATCH_PV / COMMENT_PV / SHARE_PV).
     *
     * Usa o MESMO DTO de `getLiveRoomViewTrends` — os dois endpoints devolvem
     * `{stats_type, data_points[{value, timestamp}]}` identico, so' muda o
     * enum de `statsType`.
     *
     * @return array{interactive_trend_performances: list<LiveRoomValueTrendPerformance>}
     */
    public function getLiveRoomInteractiveTrends(string $liveRoomId, string $version = '202502'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/interactive_trend_performances',
        );

        return ['interactive_trend_performances' => array_map(
            static fn (array $p): LiveRoomValueTrendPerformance => LiveRoomValueTrendPerformance::fromArray($p),
            $response['data']['interactive_trend_performances'] ?? [],
        )];
    }

    /**
     * Series de audiencia da live (TREND_ONLINE_VIEWER / TREND_ENTER_VIEWER /
     * TREND_LEFT_VIEWER). Mesmo DTO do trend interativo.
     *
     * @return array{view_trend_performances: list<LiveRoomValueTrendPerformance>}
     */
    public function getLiveRoomViewTrends(string $liveRoomId, string $version = '202502'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/view_trend_performances',
        );

        return ['view_trend_performances' => array_map(
            static fn (array $p): LiveRoomValueTrendPerformance => LiveRoomValueTrendPerformance::fromArray($p),
            $response['data']['view_trend_performances'] ?? [],
        )];
    }

    /**
     * Desempenho produto a produto DENTRO da transmissao.
     *
     * @return array{product_stats: list<LiveRoomProductStat>}
     */
    public function getLiveRoomProductStats(string $liveRoomId, string $version = '202502'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/product_stats',
        );

        return ['product_stats' => array_map(
            static fn (array $p): LiveRoomProductStat => LiveRoomProductStat::fromArray($p),
            $response['data']['product_stats'] ?? [],
        )];
    }

    /**
     * De onde veio a audiencia da live, com sub-fontes por fonte.
     *
     * @return array{traffic_performances: list<LiveRoomTrafficPerformance>}
     */
    public function getLiveRoomTrafficPerformance(string $liveRoomId, string $version = '202502'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/traffic_performances',
        );

        return ['traffic_performances' => array_map(
            static fn (array $p): LiveRoomTrafficPerformance => LiveRoomTrafficPerformance::fromArray($p),
            $response['data']['traffic_performances'] ?? [],
        )];
    }

    /**
     * Perfil demografico da audiencia da live, em 7 listas (all* = toda a
     * audiencia, paid* = so' trafego pago).
     */
    public function getLiveRoomUserPortraits(string $liveRoomId, string $version = '202502'): LiveRoomUserPortraits
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/live_rooms/".rawurlencode($liveRoomId).'/user_portraits',
        );

        return LiveRoomUserPortraits::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------------------
    // SPS (Shop Performance Score) — nota de servico da loja.
    // ------------------------------------------------------------------

    /** Painel do SPS: nota, tier, dimensoes, beneficios e ofensores. */
    public function getSpsOverview(array $filters = [], string $version = '202606'): SpsOverview
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_performances/overview", $filters);

        return SpsOverview::fromArray($response['data'] ?? []);
    }

    /**
     * Conjunto FIXO de metricas SPS da loja (NRR, NBFR, SFCR, OTDR, AHT, IM_DSAT).
     *
     * @param  array<string, mixed>  $filters
     * @return array{metrics: list<SpsMetric>}
     */
    public function getSpsMetrics(array $filters = [], string $version = '202606'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_performances/metrics", $filters);

        return ['metrics' => array_map(
            static fn (array $m): SpsMetric => SpsMetric::fromArray($m),
            $response['data']['metrics'] ?? [],
        )];
    }

    /**
     * Diagnostico de UMA metrica: benchmark, formula, distribuicao, serie
     * historica e o texto de analise.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSpsMetricDiagnosis(string $metricCode, array $filters = [], string $version = '202606'): SpsMetricDiagnosis
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_performances/metrics/".rawurlencode($metricCode).'/diagnosis',
            $filters,
        );

        return SpsMetricDiagnosis::fromArray($response['data'] ?? []);
    }

    /**
     * Pedidos/atendimentos que puxaram a metrica pra baixo. So' a lista da
     * metrica consultada vem preenchida; cada uma tem shape proprio. Paginado.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSpsMetricProblemDetails(string $metricCode, array $filters = [], string $version = '202606'): SpsMetricProblemDetails
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_performances/metrics/".rawurlencode($metricCode).'/problem_details',
            $filters,
        );

        return SpsMetricProblemDetails::fromArray($response['data'] ?? []);
    }

    /**
     * Piores ofensores da metrica agrupados (produto ou transportadora).
     * Existe so' pra NRR, NBFR, OTDR e AHT.
     *
     * @param  array<string, mixed>  $filters
     */
    public function getSpsMetricTopItems(string $metricCode, array $filters = [], string $version = '202606'): SpsMetricTopItems
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_performances/metrics/".rawurlencode($metricCode).'/top_items',
            $filters,
        );

        return SpsMetricTopItems::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------------------
    // Loja — GMV/trafego consolidados.
    // ------------------------------------------------------------------

    /**
     * Desempenho da loja no periodo. `granularity=ALL` devolve 1 intervalo;
     * `1D`, um por dia.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopPerformance, latest_available_date: ?string}
     */
    public function getShopPerformance(array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop/performance", $filters);

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopPerformance::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Desempenho da loja HORA A HORA de um dia. `$date` vai no PATH (YYYY-MM-DD,
     * fuso da loja).
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopHourlyPerformance}
     */
    public function getShopPerformancePerHour(string $date, array $filters = [], string $version = '202510'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop/performance/".rawurlencode($date).'/performance_per_hour',
            $filters,
        );

        return ['performance' => isset($response['data']['performance'])
            ? ShopHourlyPerformance::fromArray($response['data']['performance'])
            : null];
    }

    // ------------------------------------------------------------------
    // Lives da LOJA (escopo data.shop_analytics.public.read) — visao
    // agregada do vendedor, distinta dos endpoints de creator acima.
    // ------------------------------------------------------------------

    /**
     * Consolidado de todas as lives da loja no periodo.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopLiveOverviewPerformance, latest_available_date: ?string}
     */
    public function getShopLivePerformanceOverview(array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_lives/overview_performance", $filters);

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopLiveOverviewPerformance::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Lista de sessoes de LIVE da loja com venda + interacao.
     *
     * `interaction_performance` so' vem quando `account_type` e' OFFICIAL_ACCOUNTS
     * ou MARKETING_ACCOUNTS.
     *
     * @param  array<string, mixed>  $filters
     * @return array{live_stream_sessions: list<ShopLiveSessionPerformance>, latest_available_date: ?string, next_page_token: ?string, total_count: ?int}
     */
    public function getShopLivePerformanceList(array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_lives/performance", $filters);

        return [
            'live_stream_sessions' => array_map(
                static fn (array $s): ShopLiveSessionPerformance => ShopLiveSessionPerformance::fromArray($s),
                $response['data']['live_stream_sessions'] ?? [],
            ),
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Recorte MINUTO A MINUTO de uma live da loja.
     *
     * Os intervalos sao paginados; `performance.overall` e' sempre da live
     * INTEIRA, nunca da pagina — nao tente somar uma coisa na outra.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopLiveMinutePerformance, next_page_token: ?string, total_count: ?int}
     */
    public function getShopLiveMinutePerformance(string $liveId, array $filters = [], string $version = '202510'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_lives/".rawurlencode($liveId).'/performance_per_minutes',
            $filters,
        );

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopLiveMinutePerformance::fromArray($response['data']['performance'])
                : null,
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Produtos vendidos DENTRO de uma live da loja.
     *
     * Atencao ao path: e' `/shop/{live_id}/products_performance` (sem o
     * `_lives`), versao 202512 — destoa dos demais endpoints de live.
     *
     * @param  array<string, mixed>  $filters
     * @return array{products: list<ShopLiveProductPerformance>}
     */
    public function getShopLiveProductsPerformanceList(string $liveId, array $filters = [], string $version = '202512'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop/".rawurlencode($liveId).'/products_performance',
            $filters,
        );

        return ['products' => array_map(
            static fn (array $p): ShopLiveProductPerformance => ShopLiveProductPerformance::fromArray($p),
            $response['data']['products'] ?? [],
        )];
    }

    // ------------------------------------------------------------------
    // Produtos e SKUs da loja.
    // ------------------------------------------------------------------

    /**
     * Lista de produtos com desempenho quebrado por 8 canais (total, live/video/
     * card do vendedor, afiliado total/live/video e aba Shop).
     *
     * @param  array<string, mixed>  $filters
     * @return array{products: list<ShopProductPerformanceSummary>, next_page_token: ?string, total_count: ?int, latest_available_date: ?string}
     */
    public function getShopProductPerformanceList(array $filters = [], string $version = '202605'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_products/performance", $filters);

        return [
            'products' => array_map(
                static fn (array $p): ShopProductPerformanceSummary => ShopProductPerformanceSummary::fromArray($p),
                $response['data']['products'] ?? [],
            ),
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Drill-down de UM produto: intervalos, avaliacoes, top conteudos e top
     * creators. Avaliacoes/top-* sao do periodo inteiro, nao por intervalo.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopProductPerformanceDetail, latest_available_date: ?string}
     */
    public function getShopProductPerformanceDetail(string $productId, array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_products/".rawurlencode($productId).'/performance',
            $filters,
        );

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopProductPerformanceDetail::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Lista de SKUs com GMV, pedidos e unidades.
     *
     * `category_filter` e `product_ids` sao arrays na doc — mande-os como CSV,
     * que e' o formato que a query assinada aceita.
     *
     * @param  array<string, mixed>  $filters
     * @return array{skus: list<ShopSkuPerformanceSummary>, next_page_token: ?string, total_count: ?int, latest_available_date: ?string}
     */
    public function getShopSkuPerformanceList(array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_skus/performance", $filters);

        return [
            'skus' => array_map(
                static fn (array $s): ShopSkuPerformanceSummary => ShopSkuPerformanceSummary::fromArray($s),
                $response['data']['skus'] ?? [],
            ),
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Drill-down de UM SKU, com quebra de GMV e de unidades por tipo de conteudo.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopSkuPerformanceDetail, latest_available_date: ?string}
     */
    public function getShopSkuPerformanceDetail(string $skuId, array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_skus/".rawurlencode($skuId).'/performance',
            $filters,
        );

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopSkuPerformanceDetail::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    // ------------------------------------------------------------------
    // Videos.
    // ------------------------------------------------------------------

    /**
     * Consolidado de todos os videos da loja no periodo.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopVideoOverviewPerformance, latest_available_date: ?string}
     */
    public function getShopVideoPerformanceOverview(array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_videos/overview_performance", $filters);

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopVideoOverviewPerformance::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Lista de videos da loja com GMV, GPM e engajamento.
     *
     * @param  array<string, mixed>  $filters
     * @return array{videos: list<ShopVideoPerformanceSummary>, latest_available_date: ?string, next_page_token: ?string, total_count: ?int}
     */
    public function getShopVideoPerformanceList(array $filters = [], string $version = '202605'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/shop_videos/performance", $filters);

        return [
            'videos' => array_map(
                static fn (array $v): ShopVideoPerformanceSummary => ShopVideoPerformanceSummary::fromArray($v),
                $response['data']['videos'] ?? [],
            ),
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Drill-down de UM video: venda (total + por produto ancorado), trafego e
     * perfil da audiencia.
     *
     * @param  array<string, mixed>  $filters
     * @return array{performance: ?ShopVideoPerformanceDetail, latest_available_date: ?string}
     */
    public function getShopVideoPerformanceDetails(string $videoId, array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_videos/".rawurlencode($videoId).'/performance',
            $filters,
        );

        return [
            'performance' => isset($response['data']['performance'])
                ? ShopVideoPerformanceDetail::fromArray($response['data']['performance'])
                : null,
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
        ];
    }

    /**
     * Produtos promovidos em UM video, ranqueados.
     *
     * @param  array<string, mixed>  $filters
     * @return array{products: list<ShopVideoProductPerformance>, latest_available_date: ?string, next_page_token: ?string, total_count: ?int}
     */
    public function getShopVideoProductPerformanceList(string $videoId, array $filters = [], string $version = '202509'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/analytics/{$version}/shop_videos/".rawurlencode($videoId).'/products/performance',
            $filters,
        );

        return [
            'products' => array_map(
                static fn (array $p): ShopVideoProductPerformance => ShopVideoProductPerformance::fromArray($p),
                $response['data']['products'] ?? [],
            ),
            'latest_available_date' => $response['data']['latest_available_date'] ?? null,
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Metricas DIARIAS de ate' 100 videos de um MESMO autor (API de creator).
     *
     * Passe `video_ids` separado por virgula. As datas sao epoch em SEGUNDOS e a
     * hora e' ignorada — o pipeline so' fecha por dia, com 1 dia de atraso, e o
     * `start_time_ge` nao pode passar de 180 dias atras.
     *
     * @param  array<string, mixed>  $filters
     * @return array{videos: list<VideoPerformance>}
     */
    public function getVideoPerformances(array $filters = [], string $version = '202403'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/analytics/{$version}/videos/performances", $filters);

        return ['videos' => array_map(
            static fn (array $v): VideoPerformance => VideoPerformance::fromArray($v),
            $response['data']['videos'] ?? [],
        )];
    }
}
