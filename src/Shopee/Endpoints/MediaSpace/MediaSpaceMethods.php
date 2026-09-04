<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\MediaSpace;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopee\Bases\SendsMultipart;

/**
 * Midia de PRODUTO (modulo `media_space`): imagens e video dos anuncios.
 * O `image_id` devolvido aqui e' o que entra em add_item/update_item
 * (image.image_id_list) e em category_recommend; o `video_upload_id` entra
 * em add_item.video_upload_id.
 *
 * Fluxo de video: initVideoUpload (md5 + tamanho, max 30MB) -> uploadVideoPart
 * por parte de EXATAMENTE 4MB (menos a ultima), part_seq a partir de 0 ->
 * completeVideoUpload -> getVideoUploadResult ate status SUCCEEDED.
 *
 * ATENCAO ao tipo de assinatura por endpoint (vem do catalogo oficial):
 * upload_image, upload_video_part e complete_video_upload sao "Public"
 * (partner_id + timestamp + sign, sem access_token/shop_id); init, result e
 * cancel sao "Shop" (assinatura completa).
 */
class MediaSpaceMethods extends BaseMethods
{
    use SendsMultipart;

    /**
     * Sobe 1..9 imagens (JPG/JPEG/PNG, max 10MB cada) num unico multipart.
     * scene: normal (capa/galeria, cortada 1:1 ou 3:4) | desc (descricao,
     * mantida como veio). ratio (1:1 | 3:4) so' pra sellers whitelisted.
     *
     * POST /api/v2/media_space/upload_image  (Public)
     *
     * @param  array<int, array{contents: mixed, filename?: string|null}>|string  $images  lista de arquivos, ou o conteudo de UMA imagem
     * @return array<string, mixed> response{image_info{image_id,image_url_list[]}, image_info_list[]}
     */
    public function uploadImage(array|string $images, string $scene = 'normal', ?string $ratio = null, ?string $filename = null): array
    {
        if (is_string($images)) {
            $images = [['contents' => $images, 'filename' => $filename ?? 'image.jpg']];
        }

        $files = [];
        foreach (array_values($images) as $i => $image) {
            $files[] = [
                'name' => 'image',
                'contents' => $image['contents'],
                'filename' => $image['filename'] ?? ('image-'.$i.'.jpg'),
            ];
        }

        $fields = ['scene' => $scene];
        if ($ratio !== null) {
            $fields['ratio'] = $ratio;
        }

        $response = $this->postMultipart('/api/v2/media_space/upload_image', $fields, $files, publicApi: true);

        return $response['response'] ?? [];
    }

    /**
     * Abre uma sessao de upload de video. file_size em bytes (max 30MB).
     *
     * POST /api/v2/media_space/init_video_upload  (Shop)
     *
     * @return string video_upload_id
     */
    public function initVideoUpload(string $fileMd5, int $fileSize): string
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/media_space/init_video_upload', [], [
            'file_md5' => $fileMd5,
            'file_size' => $fileSize,
        ]);

        return (string) ($response['response']['video_upload_id'] ?? '');
    }

    /**
     * Envia uma parte do video (4MB exatos, exceto a ultima). part_seq inicia em 0.
     *
     * POST /api/v2/media_space/upload_video_part  (Public, multipart)
     *
     * @param  mixed  $partContent  string/resource/stream com o pedaco
     * @return array<string, mixed> envelope completo (response vazio quando ok)
     */
    public function uploadVideoPart(string $videoUploadId, int $partSeq, string $contentMd5, mixed $partContent): array
    {
        return $this->postMultipart('/api/v2/media_space/upload_video_part', [
            'video_upload_id' => $videoUploadId,
            'part_seq' => $partSeq,
            'content_md5' => $contentMd5,
        ], [
            ['name' => 'part_content', 'contents' => $partContent, 'filename' => 'part-'.$partSeq],
        ], publicApi: true);
    }

    /**
     * Fecha a sessao apos todas as partes. report_data.upload_cost = ms gastos
     * no upload (a Shopee usa pra telemetria; obrigatorio no catalogo).
     *
     * POST /api/v2/media_space/complete_video_upload  (Public)
     *
     * @param  array<int, int>  $partSeqList
     * @return array<string, mixed> envelope completo
     */
    public function completeVideoUpload(string $videoUploadId, array $partSeqList, int $uploadCostMs = 0): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/media_space/complete_video_upload', [], [
            'video_upload_id' => $videoUploadId,
            'part_seq_list' => array_values($partSeqList),
            'report_data' => ['upload_cost' => $uploadCostMs],
        ], publicApi: true);
    }

    /**
     * Status do processamento: INITIATED, TRANSCODING, SUCCEEDED, FAILED,
     * CANCELLED. Em SUCCEEDED vem video_info (urls + thumbnail + duracao).
     *
     * GET /api/v2/media_space/get_video_upload_result  (Shop)
     *
     * @return array<string, mixed> response{status, message, video_info}
     */
    public function getVideoUploadResult(string $videoUploadId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/media_space/get_video_upload_result', [
            'video_upload_id' => $videoUploadId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cancela uma sessao de upload em andamento.
     *
     * POST /api/v2/media_space/cancel_video_upload  (Shop)
     *
     * @return array<string, mixed> envelope completo
     */
    public function cancelVideoUpload(string $videoUploadId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/media_space/cancel_video_upload', [], [
            'video_upload_id' => $videoUploadId,
        ]);
    }
}
