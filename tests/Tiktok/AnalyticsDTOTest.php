<?php

declare(strict_types=1);

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

/** Conteudo de `data` do exemplo OFICIAL da doc do endpoint. */
function analyticsFixture(string $slug): array
{
    return json_decode(file_get_contents(__DIR__.'/../Fixtures/Tiktok/'.$slug.'.json'), true);
}

/**
 * Caminhos de campo que o payload trazia e o DTO NAO devolveu — recursivo.
 *
 * O teste que originou o padrao comparava so' as chaves de topo, e por isso
 * deixava passar campo descartado dentro de objeto aninhado. Aqui a comparacao
 * desce a arvore inteira (em lista, o item 0 representa a shape).
 *
 * @return list<string>
 */
function analyticsLostFields(mixed $raw, mixed $out, string $path = ''): array
{
    if (! is_array($raw)) {
        return [];
    }

    // Lista: a shape do primeiro item representa todos.
    if (array_is_list($raw)) {
        if ($raw === []) {
            return [];
        }

        return is_array($out) && isset($out[0])
            ? analyticsLostFields($raw[0], $out[0], $path.'[0]')
            : [$path.'[0]'];
    }

    $lost = [];
    foreach ($raw as $key => $value) {
        $here = $path === '' ? $key : $path.'.'.$key;
        if (! is_array($out) || ! array_key_exists($key, $out)) {
            $lost[] = $here;

            continue;
        }
        $lost = array_merge($lost, analyticsLostFields($value, $out[$key], $here));
    }

    return $lost;
}

/** @return array<string, array{0: string, 1: Closure(array): array}> */
function analyticsRoundtripCases(): array
{
    $list = static fn (string $key, string $dto): Closure => static fn (array $data): array => [
        $key => array_map(static fn (array $i): array => $dto::fromArray($i)->toArray(), $data[$key]),
    ] + array_diff_key($data, [$key => null]);

    $object = static fn (string $key, string $dto): Closure => static fn (array $data): array => [
        $key => $dto::fromArray($data[$key])->toArray(),
    ] + array_diff_key($data, [$key => null]);

    $root = static fn (string $dto): Closure => static fn (array $data): array => $dto::fromArray($data)->toArray();

    return [
        'Get Bestselling Creators' => ['get-bestselling-creators', $list('creators', BestsellingCreator::class)],
        'Get Bestselling LIVE Sessions' => ['get-bestselling-live-sessions', $list('lives', BestsellingLiveSession::class)],
        'Get Bestselling Products' => ['get-bestselling-products', $list('products', BestsellingProduct::class)],
        'Get Bestselling Videos' => ['get-bestselling-videos', $list('videos', BestsellingVideo::class)],
        'Get Live Room Core Stats' => ['get-live-room-core-stats', $object('stats', LiveRoomCoreStats::class)],
        'Get Live Room GMV Trend' => ['get-live-room-gmv-trend', $list('gmv_trend_performances', LiveRoomGmvTrendPerformance::class)],
        'Get Live Room Interactive Trends' => ['get-live-room-interactive-trends', $list('interactive_trend_performances', LiveRoomValueTrendPerformance::class)],
        'Get Live Room Product Stats' => ['get-live-room-product-stats', $list('product_stats', LiveRoomProductStat::class)],
        'Get Live Room Traffic Performance' => ['get-live-room-traffic-performance', $list('traffic_performances', LiveRoomTrafficPerformance::class)],
        'Get Live Room User Portraits' => ['get-live-room-user-portraits', $root(LiveRoomUserPortraits::class)],
        'Get Live Room View Trends' => ['get-live-room-view-trends', $list('view_trend_performances', LiveRoomValueTrendPerformance::class)],
        'Get SPS Metric Diagnosis' => ['get-sps-metric-diagnosis', $root(SpsMetricDiagnosis::class)],
        'Get SPS Metric Problem Details' => ['get-sps-metric-problem-details', $root(SpsMetricProblemDetails::class)],
        'Get SPS Metric Top Items' => ['get-sps-metric-top-items', $root(SpsMetricTopItems::class)],
        'Get SPS Metrics' => ['get-sps-metrics', $list('metrics', SpsMetric::class)],
        'Get SPS Overview' => ['get-sps-overview', $root(SpsOverview::class)],
        'Get Shop LIVE Minute Performance' => ['get-shop-live-minute-performance', $object('performance', ShopLiveMinutePerformance::class)],
        'Get Shop LIVE Performance List' => ['get-shop-live-performance-list', $list('live_stream_sessions', ShopLiveSessionPerformance::class)],
        'Get Shop LIVE Performance Overview' => ['get-shop-live-performance-overview', $object('performance', ShopLiveOverviewPerformance::class)],
        'Get Shop LIVE Products Performance List' => ['get-shop-live-products-performance-list', $list('products', ShopLiveProductPerformance::class)],
        'Get Shop Performance' => ['get-shop-performance', $object('performance', ShopPerformance::class)],
        'Get Shop Performance Per Hour' => ['get-shop-performance-per-hour', $object('performance', ShopHourlyPerformance::class)],
        'Get Shop Product Performance Detail' => ['get-shop-product-performance-detail', $object('performance', ShopProductPerformanceDetail::class)],
        'Get Shop Product Performance List' => ['get-shop-product-performance-list', $list('products', ShopProductPerformanceSummary::class)],
        'Get Shop SKU Performance Detail' => ['get-shop-sku-performance-detail', $object('performance', ShopSkuPerformanceDetail::class)],
        'Get Shop SKU Performance List' => ['get-shop-sku-performance-list', $list('skus', ShopSkuPerformanceSummary::class)],
        'Get Shop Video Performance Details' => ['get-shop-video-performance-details', $object('performance', ShopVideoPerformanceDetail::class)],
        'Get Shop Video Performance List' => ['get-shop-video-performance-list', $list('videos', ShopVideoPerformanceSummary::class)],
        'Get Shop Video Performance Overview' => ['get-shop-video-performance-overview', $object('performance', ShopVideoOverviewPerformance::class)],
        'Get Shop Video Product Performance List' => ['get-shop-video-product-performance-list', $list('products', ShopVideoProductPerformance::class)],
        'Get Video Performances' => ['get-video-performances', $list('videos', VideoPerformance::class)],
    ];
}

it('nao descarta NENHUM campo do exemplo OFICIAL da doc', function (string $slug, Closure $roundtrip) {
    $payload = analyticsFixture($slug);

    $perdidos = analyticsLostFields($payload, $roundtrip($payload));

    expect($perdidos)->toBe([], 'campos descartados: '.implode(', ', $perdidos));
})->with(analyticsRoundtripCases());

it('faz roundtrip LOSSLESS de valor, nao so de chave', function (string $slug, Closure $roundtrip) {
    $payload = analyticsFixture($slug);

    // Ordem de chave nao importa; VALOR importa. Este teste pega o caso que a
    // comparacao por chave deixa passar: campo declarado []string na doc que a
    // API manda como string crua — tipar `array` devolveria [] e a chave
    // continuaria la', mas o valor teria sumido.
    $normalize = function (mixed $v) use (&$normalize): mixed {
        if (! is_array($v)) {
            return $v;
        }
        $v = array_map($normalize, $v);
        if (! array_is_list($v)) {
            ksort($v);
        }

        return $v;
    };

    expect($normalize($roundtrip($payload)))->toEqual($normalize($payload));
})->with(analyticsRoundtripCases());

it('mantem dinheiro e taxa como STRING (nunca float)', function () {
    $live = ShopLiveMinutePerformance::fromArray(analyticsFixture('get-shop-live-minute-performance')['performance']);

    // "39440.00" nao pode virar 39440.0: a casa decimal some na re-serializacao.
    expect($live->overall->gmv->amount)->toBe('39440.00')
        ->and($live->intervals[0]->conversion->avgPrice->amount)->toBe('7.29')
        // Taxas sao FRACAO em string ("0.3264" = 32,64%), nao percentual.
        ->and($live->intervals[0]->traffic->ctr)->toBe('0.3264')
        ->and($live->intervals[0]->conversion->gpm->watchGpm)->toBe('158.18');
});

it('mistura formato de taxa entre endpoints de live — fracao aqui, percentual ali', function () {
    $sessao = ShopLiveSessionPerformance::fromArray(
        analyticsFixture('get-shop-live-performance-list')['live_stream_sessions'][0],
    );
    $minuto = ShopLiveMinutePerformance::fromArray(analyticsFixture('get-shop-live-minute-performance')['performance']);

    // Get Shop LIVE Performance List devolve JA' PERCENTUAL, com o sinal de %...
    expect($sessao->salesPerformance->clickToOrderRate)->toBe('18%')
        ->and($sessao->interactionPerformance->clickThroughRate)->toBe('13.99%')
        // ...enquanto o performance_per_minutes devolve fracao. Normalizar os
        // dois do mesmo jeito erra por 100x.
        ->and($minuto->intervals[0]->conversion->clickToOrderRate->skuOrderCtor)->toBe('0.0187');
});

it('le a chave crua que comeca com digito e o typo `produt_clicks` da API', function () {
    $sessao = ShopLiveSessionPerformance::fromArray(
        analyticsFixture('get-shop-live-performance-list')['live_stream_sessions'][0],
    );
    $produto = ShopLiveProductPerformance::fromArray(
        analyticsFixture('get-shop-live-products-performance-list')['products'][0],
    );

    // `24h_live_gmv` nao sai de nenhuma conversao camel->snake: exige #[JsonKey].
    // E nao e' o GMV da live — e' o pago em ate' 24h APOS assistir.
    expect($sessao->salesPerformance->gmv24hLive->amount)->toBe('340')
        ->and($sessao->salesPerformance->gmv->amount)->toBe('99')
        // `produt_clicks` esta' escrito errado NA API; o DTO le' a chave torta.
        ->and($produto->traffic->productClicks)->toBe(1346);
});

it('so preenche a lista da metrica SPS consultada, nunca as seis', function () {
    $problemas = SpsMetricProblemDetails::fromArray(analyticsFixture('get-sps-metric-problem-details'));

    // O exemplo da doc mostra todas preenchidas, mas em producao so' vem a do
    // `metric_code` pedido — as outras chegam ausentes (null), nao vazias.
    $vazio = SpsMetricProblemDetails::fromArray(['metric_code' => 'OTDR', 'total_count' => 0]);

    expect($vazio->metricCode)->toBe('OTDR')
        ->and($vazio->nrrProblemOrderItems)->toBeNull()
        ->and($vazio->otdrProblemOrderItems)->toBeNull()
        // Cada lista tem shape PROPRIA: NRR traz nota de review, OTDR traz prazo.
        ->and($problemas->nrrProblemOrderItems[0]->userReviewRating)->toBe(2)
        ->and($problemas->otdrProblemOrderItems[0]->expectDeliverTime)->toBe(1761523200);
});

it('separa score 0..5 do valor bruto da metrica no SPS', function () {
    $overview = SpsOverview::fromArray(analyticsFixture('get-sps-overview'));
    $diagnostico = SpsMetricDiagnosis::fromArray(analyticsFixture('get-sps-metric-diagnosis'));

    expect($overview->spsScore)->toBe('4.6')
        // tier (enum) e tierText (rotulo) DIVERGEM no proprio exemplo oficial:
        // nao derive um do outro.
        ->and($overview->spsTier)->toBe('GOOD')
        ->and($overview->spsTierText)->toBe('Excellent')
        // peso da dimensao e' 0..100; score e' 0..5 — escalas diferentes.
        ->and($overview->dimensions[0]->weight)->toBe('70')
        ->and($overview->dimensions[0]->score)->toBe('4.8')
        // score da metrica (0..5) != valor da metrica (unidade em valueUnit)
        ->and($diagnostico->score)->toBe('4.3')
        ->and($diagnostico->value)->toBe('0.947')
        ->and($diagnostico->valueUnit)->toBe('PERCENT')
        // data da serie e' yyyyMMdd em string, nao epoch nem ISO
        ->and($diagnostico->trend->dataPoints[0]->recordDate)->toBe('20260601');
});

it('trata `amount` de items_sold_breakdown como CONTAGEM, nao dinheiro', function () {
    $sku = ShopSkuPerformanceDetail::fromArray(analyticsFixture('get-shop-sku-performance-detail')['performance']);
    $intervalo = $sku->intervals[0];

    // Mesmo nome de campo, dois tipos: no gmv_breakdown e' string monetaria...
    expect($intervalo->gmvBreakdown[0]->amount)->toBe('82.15')
        // ...no items_sold_breakdown e' int de unidades.
        ->and($intervalo->itemsSoldBreakdown[0]->amount)->toBe(10)
        // A doc declara product_id INT e sku_id STRING no MESMO objeto.
        ->and($sku->productId)->toBe(123456789)
        ->and($sku->skuId)->toBe('123456789');
});

it('nao mistura os canais do produto: cada um tem nome de GMV proprio', function () {
    $produto = ShopProductPerformanceSummary::fromArray(
        analyticsFixture('get-shop-product-performance-list')['products'][0],
    );

    expect($produto->totalPerformance->gmv->amount)->toBe('395.03')
        ->and($produto->sellerLivePerformance->attributedGmv->amount)->toBe('142.8')
        // afiliado troca o nome do campo: live_attributed_gmv / attributed_video_gmv
        ->and($produto->affiliateLivePerformance->liveAttributedGmv->amount)->toBe('121.4')
        ->and($produto->affiliateVideoPerformance->attributedVideoGmv->amount)->toBe('82.9')
        // ...e troca add_cart_users por atc_users
        ->and($produto->affiliateLivePerformance->atcUsers)->toBe(12)
        ->and($produto->sellerLivePerformance->addCartUsers)->toBe(17)
        // aba Shop repete o prefixo em tudo
        ->and($produto->shopTabPerformance->shopTabGmv->amount)->toBe('128.4');
});

it('reusa o mesmo DTO nos dois trends escalares da live', function () {
    $interativo = LiveRoomValueTrendPerformance::fromArray(
        analyticsFixture('get-live-room-interactive-trends')['interactive_trend_performances'][0],
    );
    $views = LiveRoomValueTrendPerformance::fromArray(
        analyticsFixture('get-live-room-view-trends')['view_trend_performances'][0],
    );

    // Mesma classe, mesmo shape — o que muda e' so' o enum de stats_type.
    expect($interativo->statsType)->toBe('WATCH_PV')
        ->and($views->statsType)->toBe('TREND_ONLINE_VIEWER')
        // `value` e' STRING mesmo sendo contagem inteira.
        ->and($interativo->dataPoints[0]->value)->toBe('456')
        ->and($views->dataPoints[0]->value)->toBe('123');
});

it('preserva campo []string que a API manda como string crua', function () {
    $diagnostico = SpsMetricDiagnosis::fromArray(analyticsFixture('get-sps-metric-diagnosis'));
    $produto = BestsellingProduct::fromArray(analyticsFixture('get-bestselling-products')['products'][0]);

    // A doc declara []string, o exemplo oficial manda string. Tipar `array`
    // devolveria [] e apagaria o texto em silencio — por isso `mixed`.
    expect($diagnostico->analyses[0]->summaries)->toBe('Stable performance. Top 3 issues to address.')
        ->and($produto->productImage->urls)->toBeString()
        // ja' thumb_urls vem lista mesmo, e continua lista.
        ->and($produto->productImage->thumbUrls)->toBeArray();
});

it('so publica FAIXA de GMV nos rankings de bestseller', function () {
    $creator = BestsellingCreator::fromArray(analyticsFixture('get-bestselling-creators')['creators'][0]);
    $video = BestsellingVideo::fromArray(analyticsFixture('get-bestselling-videos')['videos'][0]);

    // Nao existe GMV absoluto nesses endpoints — quem precisar do numero tem
    // que ir nos endpoints de performance da propria loja.
    expect($creator->gmvRange)->toBe('$97K ~ $191K')
        ->and($video->gmvRange)->toBe('$97K ~ $191K')
        ->and($video->productInfos[0]->productId)->toBe('1731736614984386107');
});

it('conta pedido de tres jeitos diferentes na live', function () {
    $stats = LiveRoomCoreStats::fromArray(analyticsFixture('get-live-room-core-stats')['stats']);
    $intervalo = ShopLiveMinutePerformance::fromArray(
        analyticsFixture('get-shop-live-minute-performance')['performance'],
    )->intervals[0];

    // created (inclui nao pago) != paid (pago) != buyer (usuario unico)
    expect($stats->createdOrderCount)->toBe(123)
        ->and($stats->paidOrderCount)->toBe(123)
        ->and($stats->buyerCount)->toBe(123)
        // no recorte por minuto, sku_orders (pagos) e main_orders (compras)
        // sao numeros distintos do mesmo intervalo
        ->and($intervalo->sales->skuOrders)->toBe(57)
        ->and($intervalo->sales->mainOrders)->toBe(51)
        ->and($intervalo->conversion->createdSkuOrders)->toBe(12);
});
