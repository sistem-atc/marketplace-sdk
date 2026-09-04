<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Grade;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Grades (`/v1/grades`) e variações (`/v1/grade/{id}/variacao`). Paginação `limit/offset`.
 */
class GradeMethods extends BaseMethods
{
    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'grades/', array_merge(['limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "grades/{$id}/");
    }

    /** @return array<string,mixed> */
    public function create(string $nome, string $nomeVisivel): array
    {
        return $this->makeRequest(HttpMethod::POST, 'grades/', [], ['nome' => $nome, 'nome_visivel' => $nomeVisivel]);
    }

    /** @return array<string,mixed> */
    public function createVariation(int|string $gradeId, string $nome): array
    {
        return $this->makeRequest(HttpMethod::POST, "grade/{$gradeId}/variacao/", [], ['nome' => $nome]);
    }
}
