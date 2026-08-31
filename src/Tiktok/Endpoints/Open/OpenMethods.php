<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Open;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Open\UploadFileInitResponseDTO;

/**
 * Familia `/open/...` — utilitarios transversais da OpenAPI do TikTok Shop,
 * que nao pertencem a nenhum dominio de negocio (pedido, produto, financeiro).
 *
 * Ficou em grupo proprio de proposito: o unico endpoint hoje e' a abertura de
 * upload de arquivo em chunks, que serve produto, atendimento e criativo ao
 * mesmo tempo. Enfiar isso em Authorization (OAuth/lojas/shop_cipher) ou em
 * Product amarraria um utilitario generico a um dominio que ele nao tem.
 */
class OpenMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202512';

    /**
     * Abre a sessao de upload em chunks e devolve destino + token.
     *
     * NAO envia arquivo: e' so' o handshake. O upload em si vai chunk a chunk
     * pra `upload_url` devolvida, autenticado pelo `upload_token` — outro host,
     * fora da assinatura HMAC desta API. Hoje o TikTok so' aceita `file_type`
     * video, e `total_chunk_count` tem que bater com o numero de partes que
     * voce realmente vai enviar.
     *
     * @param  int  $fileSize  em BYTES
     * @param  string  $targetPath  onde o video sera' amarrado depois de subir
     * @param  string|null  $categoryAssetCipher  obrigatorio pra loja cross-border; sem ele a chamada e' recusada
     */
    public function uploadFileInit(
        string $fileName,
        string $fileType,
        int $fileSize,
        int $totalChunkCount,
        string $targetPath,
        ?string $categoryAssetCipher = null,
        string $version = self::DEFAULT_VERSION,
    ): UploadFileInitResponseDTO {
        $query = [];
        if ($categoryAssetCipher !== null) {
            $query['category_asset_cipher'] = $categoryAssetCipher;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/open/{$version}/file/init",
            $query,
            [
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'total_chunk_count' => $totalChunkCount,
                'target_path' => $targetPath,
            ],
        );

        return UploadFileInitResponseDTO::fromArray($response['data'] ?? []);
    }
}
