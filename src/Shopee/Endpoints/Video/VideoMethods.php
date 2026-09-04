<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Video;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\UserApiMethods;

/**
 * Modulo `/api/v2/video` — Shopee Video (publicacao e analytics dos videos
 * curtos do vendedor).
 *
 * API de tipo **User**: autentica com `user_id` (ver UserApiMethods).
 *
 * O upload do arquivo NAO mora aqui: os bytes sobem pelo modulo media_space
 * (`init_video_upload` → `upload_video_part` → `complete_video_upload` →
 * `get_video_upload_result`), que devolve o `video_upload_id`. Este modulo
 * so' escolhe a capa (getCoverList), preenche a ficha (editVideoInfo),
 * publica (postVideo) e le metricas.
 *
 * Analytics: dados com atraso de pelo menos 1 dia. `periodType` ∈
 * Day|Week|Month|Last7d|Last15d|Last30d e `endDate` (YYYY-MM-DD) precisa
 * casar com o periodo: Day/Last* = antes de hoje; Week = um domingo;
 * Month = ultimo dia do mes. Listagens: `page_no` a partir de 1, `page_size`
 * maximo 20.
 *
 * Todos os retornos sao o `response` cru da API (sem DTO).
 */
class VideoMethods extends UserApiMethods
{
    // ------------------------------------------------------------ publicacao

    /**
     * Frames candidatos a capa do video ja subido (lista de `cover_image_url`).
     *
     * @return array<string, mixed>
     */
    public function getCoverList(string $videoUploadId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_cover_list', [
            'video_upload_id' => $videoUploadId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Preenche a ficha de ate 5 videos (caption, capa, ate 6 produtos,
     * duet/stitch, agendamento). Cada item de `$videoUploadList`:
     * `video_upload_id`, `cover_image_url`, `allow_info{allow_duet,allow_stitch}`,
     * `scheduled_info{scheduled_post,scheduled_post_time?}` obrigatorios;
     * `caption` e `item_info[{item_id,custom_item_name?}]` opcionais.
     * `aigcLabel` = true marca o video como conteudo gerado/assistido por IA.
     * Devolve `success_list` + `failure_list`.
     *
     * @param  list<array<string, mixed>>  $videoUploadList
     * @return array<string, mixed>
     */
    public function editVideoInfo(array $videoUploadList, bool $aigcLabel = false): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/video/edit_video_info', [], [
            'video_upload_list' => array_values($videoUploadList),
            'aigc_label' => $aigcLabel,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Publica no Shopee Video os rascunhos ja editados (ate 5 por chamada).
     *
     * @param  list<string>  $videoUploadIdList
     * @return array<string, mixed>
     */
    public function postVideo(array $videoUploadIdList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/video/post_video', [], [
            'video_upload_id_list' => array_values($videoUploadIdList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Apaga videos. Rascunho = `videoUploadIdList`; publicado = `postIdList`.
     * A API aceita SO' UM dos dois por chamada.
     *
     * @param  list<string>|null  $videoUploadIdList
     * @param  list<string>|null  $postIdList
     * @return array<string, mixed>
     */
    public function deleteVideo(?array $videoUploadIdList = null, ?array $postIdList = null): array
    {
        if (($videoUploadIdList === null) === ($postIdList === null)) {
            throw new InvalidArgumentException('deleteVideo exige exatamente um de videoUploadIdList ou postIdList.');
        }

        $body = $videoUploadIdList !== null
            ? ['video_upload_id_list' => array_values($videoUploadIdList)]
            : ['post_id_list' => array_values($postIdList ?? [])];

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/video/delete_video', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Detalhe do video (status 200 DRAFT / 300 POSTED / 400 DELETED /
     * 500 SCHEDULED / 600 SCHEDULED_FAILED, urls, caption, produtos).
     * Rascunho = `videoUploadId`; publicado = `postId`. SO' UM dos dois.
     *
     * @return array<string, mixed>
     */
    public function getVideoDetail(?string $videoUploadId = null, ?string $postId = null): array
    {
        if (($videoUploadId === null) === ($postId === null)) {
            throw new InvalidArgumentException('getVideoDetail exige exatamente um de videoUploadId ou postId.');
        }

        $query = $videoUploadId !== null
            ? ['video_upload_id' => $videoUploadId]
            : ['post_id' => $postId];

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_detail', $query);

        return $response['response'] ?? [];
    }

    /**
     * Lista videos: `listType` 1 = rascunhos, 2 = publicados.
     *
     * @return array<string, mixed>
     */
    public function getVideoList(int $listType = 2, int $pageNo = 1, int $pageSize = 20): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_list', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'list_type' => $listType,
        ]);

        return $response['response'] ?? [];
    }

    // -------------------------------------------------- analytics agregado

    /**
     * Performance geral de todos os videos publicados no periodo.
     *
     * @return array<string, mixed>
     */
    public function getOverviewPerformance(string $periodType, string $endDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_overview_performance', [
            'period_type' => $periodType,
            'end_date' => $endDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Tendencia (serie) dos indicadores agregados no periodo.
     *
     * @return array<string, mixed>
     */
    public function getMetricTrend(string $periodType, string $endDate): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_metric_trend', [
            'period_type' => $periodType,
            'end_date' => $endDate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Demografia da audiencia (genero, idade, regiao). Sem parametros.
     *
     * @return array<string, mixed>
     */
    public function getUserDemographics(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_user_demographics');

        return $response['response'] ?? [];
    }

    /**
     * Performance video a video no periodo. `orderBy` ∈ Views|Likes|Comments|
     * AvgViewsDuration; `sort` asc|desc; `caption` filtra por descricao.
     *
     * @return array<string, mixed>
     */
    public function getVideoPerformanceList(
        string $periodType,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 20,
        string $orderBy = 'Views',
        string $sort = 'desc',
        ?string $caption = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_performance_list', array_filter([
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'period_type' => $periodType,
            'end_date' => $endDate,
            'order_by' => $orderBy,
            'sort' => $sort,
            'caption' => $caption,
        ], static fn ($v) => $v !== null));

        return $response['response'] ?? [];
    }

    /**
     * Performance dos PRODUTOS vinculados a videos no periodo. `orderBy` ∈
     * PlacedOrders|PlacedSales|PlacedUniqueBuyers|ConfirmedOrders|
     * ConfirmedSales|ConfirmedUniqueBuyers; filtros opcionais por
     * `itemId`/`itemName`.
     *
     * ⚠️ O path oficial tem o typo `get_prodcut_performance_list` — e' assim
     * mesmo na API; nao corrija.
     *
     * @return array<string, mixed>
     */
    public function getProductPerformanceList(
        string $periodType,
        string $endDate,
        int $pageNo = 1,
        int $pageSize = 20,
        string $orderBy = 'PlacedOrders',
        string $sort = 'desc',
        ?int $itemId = null,
        ?string $itemName = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_prodcut_performance_list', array_filter([
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'period_type' => $periodType,
            'end_date' => $endDate,
            'order_by' => $orderBy,
            'sort' => $sort,
            'item_id' => $itemId,
            'item_name' => $itemName,
        ], static fn ($v) => $v !== null));

        return $response['response'] ?? [];
    }

    // ------------------------------------------------ analytics por video

    /**
     * Performance detalhada de UM video publicado.
     *
     * @return array<string, mixed>
     */
    public function getVideoDetailPerformance(string $postId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_detail_performance', [
            'post_id' => $postId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Serie de UM indicador de UM video. `metricName` ∈ Views|Likes|Comments|
     * Shares|FollowersGrowth|PlacedOrders|PlacedSales|UniqueBuyers|
     * ConversionRate|SoldItems|SalesPerOrder|SalesPerBuyer.
     *
     * @return array<string, mixed>
     */
    public function getVideoDetailMetricTrend(string $postId, string $metricName): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_detail_metric_trend', [
            'post_id' => $postId,
            'metric_name' => $metricName,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Distribuicao da audiencia de UM video.
     *
     * @return array<string, mixed>
     */
    public function getVideoDetailAudienceDistribution(string $postId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_detail_audience_distribution', [
            'post_id' => $postId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Performance dos produtos vinculados a UM video (paginado, filtros
     * opcionais por `itemId`/`itemName`).
     *
     * @return array<string, mixed>
     */
    public function getVideoDetailProductPerformance(
        string $postId,
        int $pageNo = 1,
        int $pageSize = 20,
        ?int $itemId = null,
        ?string $itemName = null,
    ): array {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/video/get_video_detail_product_performance', array_filter([
            'page_no' => $pageNo,
            'page_size' => $pageSize,
            'post_id' => $postId,
            'item_id' => $itemId,
            'item_name' => $itemName,
        ], static fn ($v) => $v !== null));

        return $response['response'] ?? [];
    }
}
