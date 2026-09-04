<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Bases;

use Illuminate\Http\Client\Response;

/**
 * Upload multipart (imagem/video) pra Shopee Open Platform v2.
 *
 * A base (`makeRequest`) manda JSON; os endpoints de midia exigem
 * multipart/form-data. Este trait monta a query de autenticacao com o mesmo
 * `buildAuthQuery()` da base, usa o multipartClient() da base (o client da
 * factory tem asJson() e mandaria multipart rotulado como JSON) e aplica a mesma
 * regra de erro: HTTP != 2xx OU `error` preenchido no JSON -> ShopeeRequestException.
 *
 * Uso: classes que `extends BaseMethods`.
 */
trait SendsMultipart
{
    /**
     * @param  array<string, mixed>  $fields  campos simples do form (scene, part_seq...)
     * @param  array<int, array{name: string, contents: mixed, filename?: string|null}>  $files  arquivos (contents = string/resource/stream)
     * @return array<string, mixed> JSON completo devolvido pela Shopee
     */
    protected function postMultipart(string $apiPath, array $fields, array $files, bool $publicApi = false): array
    {
        $apiPath = $this->normalizeApiPath($apiPath);
        $authQuery = $this->buildAuthQuery($apiPath, $publicApi);

        $client = $this->multipartClient();
        foreach ($files as $file) {
            $client = $client->attach($file['name'], $file['contents'], $file['filename'] ?? null);
        }

        // Multipart nao tem tipo: manda tudo como string (part_seq int -> "0").
        $fields = array_map(static fn ($v) => is_bool($v) ? ($v ? 'true' : 'false') : (string) $v, $fields);

        /** @var Response $response */
        $response = $client->post($apiPath.'?'.http_build_query($authQuery), $fields);

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) {
            $this->handleError($response);
        }

        return $data;
    }
}
