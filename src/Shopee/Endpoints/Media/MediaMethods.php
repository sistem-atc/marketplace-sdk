<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Media;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopee\Bases\SendsMultipart;

/**
 * Midia GENERICA (modulo `media`, 2025+): imagens/videos usados fora do
 * cadastro de produto — devolucoes (business 2 = Return), video de
 * anuncio/afiliado (business 3) etc. Cada endpoint recebe `business` +
 * `scene` que definem as regras (formato, tamanho, duracao); consulte o
 * catalogo antes de escolher os codigos.
 *
 * Todos os 6 endpoints sao "Public" no catalogo oficial: assinatura
 * partner_id + timestamp + sign, sem access_token/shop_id.
 *
 * Fluxo de video: initVideoUpload devolve video_upload_id + part_size (o
 * tamanho de cada parte NAO e' fixo em 4MB como no media_space — use o
 * part_size retornado) -> uploadVideoPart -> completeVideoUpload ->
 * getVideoUploadResult ate SUCCEEDED.
 */
class MediaMethods extends BaseMethods
{
    use SendsMultipart;

    /**
     * Sobe imagens no contexto business/scene (ex.: 2/... pra evidencia de devolucao).
     *
     * POST /api/v2/media/upload_image  (Public, multipart)
     *
     * @param  array<int, array{contents: mixed, filename?: string|null}>|string  $images  lista de arquivos, ou o conteudo de UMA imagem
     * @return array<int, array<string, mixed>> response.image_list[] {image_id, image_url}
     */
    public function uploadImage(int $business, int $scene, array|string $images, ?string $filename = null): array
    {
        if (is_string($images)) {
            $images = [['contents' => $images, 'filename' => $filename ?? 'image.jpg']];
        }

        $files = [];
        foreach (array_values($images) as $i => $image) {
            $files[] = [
                'name' => 'images',
                'contents' => $image['contents'],
                'filename' => $image['filename'] ?? ('image-'.$i.'.jpg'),
            ];
        }

        $response = $this->postMultipart('/api/v2/media/upload_image', [
            'business' => $business,
            'scene' => $scene,
        ], $files, publicApi: true);

        return $response['response']['image_list'] ?? [];
    }

    /**
     * Abre sessao de upload de video. file_size em bytes, duration em segundos.
     *
     * POST /api/v2/media/init_video_upload  (Public)
     *
     * @return array<string, mixed> response{video_upload_id, part_size}
     */
    public function initVideoUpload(int $business, int $scene, string $fileName, int $fileSize, int $duration): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/media/init_video_upload', [], [
            'business' => $business,
            'scene' => $scene,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'duration' => $duration,
        ], publicApi: true);

        return $response['response'] ?? [];
    }

    /**
     * Envia uma parte (tamanho = part_size do init, exceto a ultima). part_seq a partir de 0.
     *
     * POST /api/v2/media/upload_video_part  (Public, multipart)
     *
     * @param  mixed  $partContent  string/resource/stream com o pedaco
     * @return array<string, mixed> envelope completo
     */
    public function uploadVideoPart(string $videoUploadId, int $partSeq, string $partMd5, mixed $partContent): array
    {
        return $this->postMultipart('/api/v2/media/upload_video_part', [
            'video_upload_id' => $videoUploadId,
            'part_seq' => $partSeq,
            'part_md5' => $partMd5,
        ], [
            ['name' => 'part_content', 'contents' => $partContent, 'filename' => 'part-'.$partSeq],
        ], publicApi: true);
    }

    /**
     * Fecha a sessao apos todas as partes.
     *
     * POST /api/v2/media/complete_video_upload  (Public)
     *
     * @return array<string, mixed> envelope completo
     */
    public function completeVideoUpload(string $videoUploadId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/media/complete_video_upload', [], [
            'video_upload_id' => $videoUploadId,
        ], publicApi: true);
    }

    /**
     * Status do processamento (SUCCEEDED traz video_info com video_url/thumbnail).
     *
     * GET /api/v2/media/get_video_upload_result  (Public)
     *
     * @return array<string, mixed> response{status, reason, update_time, video_info}
     */
    public function getVideoUploadResult(string $videoUploadId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/media/get_video_upload_result', [
            'video_upload_id' => $videoUploadId,
        ], publicApi: true);

        return $response['response'] ?? [];
    }

    /**
     * Cancela a sessao de upload.
     *
     * POST /api/v2/media/cancel_video_upload  (Public)
     *
     * @return array<string, mixed> envelope completo
     */
    public function cancelVideoUpload(string $videoUploadId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/media/cancel_video_upload', [], [
            'video_upload_id' => $videoUploadId,
        ], publicApi: true);
    }
}
