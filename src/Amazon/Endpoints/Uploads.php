<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Uploads API v2020-11-01 — destino pré-assinado pra upload de arquivos
 * (ex.: imagens de A+ Content, anexos de Messaging).
 */
class Uploads
{
    private const BASE = '/uploads/2020-11-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Cria um destino de upload pra um recurso. POST /uploadDestinations/{resource} — 10 req/s, burst 10.
     * Dado em `payload` (uploadDestinationId, url, headers). Depois faz-se PUT do
     * arquivo direto na `url` (fora da SP-API).
     *
     * @param  string  $resource  ex.: "aplus/2020-11-01/contentDocuments"
     * @param  list<string>  $marketplaceIds
     * @param  string  $contentMD5  hash MD5 base64 do arquivo
     * @param  array<string, mixed>  $query  contentType
     */
    public function createUploadDestinationForResource(
        string $resource,
        array $marketplaceIds,
        string $contentMD5,
        array $query = [],
    ): array {
        $q = array_merge([
            'marketplaceIds' => implode(',', $marketplaceIds),
            'contentMD5' => $contentMD5,
        ], $query);

        // {resource} pode conter "/" — só os segmentos são encodados.
        $path = implode('/', array_map('rawurlencode', explode('/', trim($resource, '/'))));

        return $this->client->post(self::BASE.'/uploadDestinations/'.$path.'?'.http_build_query($q));
    }
}
