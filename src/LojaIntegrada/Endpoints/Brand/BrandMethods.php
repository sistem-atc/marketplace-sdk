<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Brand;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Marcas (`/v1/marca`) e imagem da marca (`/v1/marca_imagem`).
 * Paginação `limit/offset`; `id_externo=1` busca pelo id externo.
 */
class BrandMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'marca/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, "marca/{$id}/", $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<int,int|string> $ids ids separados por `;` no path
     * @return array<string,mixed>
     */
    public function getSet(array $ids, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::GET, 'marca/set/'.implode(';', $ids).'/', $byExternalId ? ['id_externo' => 1] : []);
    }

    /**
     * @param array<string,mixed> $data nome, apelido, descricao, id_externo
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'marca/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int|string $id, array $data, bool $byExternalId = false): array
    {
        return $this->makeRequest(HttpMethod::PUT, "marca/{$id}/", $byExternalId ? ['id_externo' => 1] : [], $data);
    }

    /** @return array<string,mixed> */
    public function delete(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "marca/{$id}/");
    }

    /**
     * Troca a imagem da marca por URL (PATCH `marca_imagem/{id}`).
     *
     * @return array<string,mixed>
     */
    public function updateImageByUrl(int|string $imageId, string $imageUrl): array
    {
        return $this->makeRequest(HttpMethod::PATCH, "marca_imagem/{$imageId}/", [], ['imagem_url' => $imageUrl]);
    }

    /**
     * Troca a imagem da marca por arquivo (multipart, campo `arquivo`).
     *
     * @param string $contents conteúdo binário do arquivo
     * @return array<string,mixed>
     */
    public function updateImageByFile(int|string $imageId, string $contents, string $filename = 'imagem.jpg'): array
    {
        return $this->makeRequest(
            HttpMethod::PATCH,
            "marca_imagem/{$imageId}/arquivo/",
            [],
            ['arquivo' => ['name' => 'arquivo', 'contents' => $contents, 'filename' => $filename]],
            multipart: true,
        );
    }

    /** @return array<string,mixed> */
    public function deleteImage(int|string $imageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "marca_imagem/{$imageId}/");
    }
}
