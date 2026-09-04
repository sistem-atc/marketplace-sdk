<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Concerns;

/**
 * Paginacao por cursor da Admin API REST (Link header -> page_info).
 * Mesmo padrao de OrderMethods::eachByPeriod, generalizado pra qualquer
 * recurso que lista: a 1a pagina leva os filtros, as seguintes SO' page_info+limit.
 */
trait PaginatesRestByCursor
{
    /**
     * Itera TODOS os itens de `$path` (chave `$key` do JSON), pagina a pagina.
     *
     * @param  array<string, mixed>  $query  filtros da 1a pagina
     * @return \Generator<int, array<string, mixed>>
     */
    protected function eachPage(string $path, string $key, array $query = [], int $limit = 250): \Generator
    {
        $path = $this->normalizePath($path);
        $query = array_merge($query, ['limit' => $limit]);

        $attempt = 0;
        while (true) {
            $response = $this->httpClient->get($path, $query);

            if (($response->status() === 429 || $response->status() >= 500) && $attempt < 3) {
                $attempt++;
                sleep((int) ($response->header('Retry-After') ?: 2 ** $attempt));

                continue;
            }
            $attempt = 0;

            if ($response->failed()) {
                $this->handleError($response);
            }

            foreach (($response->json($key) ?? []) as $item) {
                yield $item;
            }

            $pageInfo = $this->cursorNextPageInfo($response->header('Link'));
            if ($pageInfo === null) {
                break;
            }

            $query = ['limit' => $limit, 'page_info' => $pageInfo];
        }
    }

    /**
     * Extrai o `page_info` do segmento rel="next" do Link header. null = fim.
     */
    protected function cursorNextPageInfo(?string $linkHeader): ?string
    {
        if ($linkHeader === null || $linkHeader === '') {
            return null;
        }

        foreach (explode(',', $linkHeader) as $segment) {
            if (! str_contains($segment, 'rel="next"')) {
                continue;
            }
            if (preg_match('/[?&]page_info=([^&>]+)/', $segment, $m)) {
                return urldecode($m[1]);
            }
        }

        return null;
    }
}
