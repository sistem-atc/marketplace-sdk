<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\HtmlCode;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Códigos HTML injetados na loja (`/v1/codigo_html`). Paginação `limit/offset`.
 */
class HtmlCodeMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'codigo_html/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "codigo_html/{$id}/");
    }

    /**
     * @param array<string,mixed> $data conteudo, tipo (html/css/js), descricao, pagina_publicacao, local_publicacao
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'codigo_html/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(int|string $id, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "codigo_html/{$id}/", [], $data);
    }
}
