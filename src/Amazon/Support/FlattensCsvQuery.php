<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Support;

/**
 * A SP-API recebe parâmetros de query multi-valor (marketplaceIds,
 * reportTypes, includedData…) como CSV (`collectionFormat: csv`), não como
 * `key[]=a&key[]=b` (que é o que http_build_query gera). Este trait converte
 * todo valor array da query em string separada por vírgula; bool vira
 * `true`/`false` literal (Swagger boolean).
 */
trait FlattensCsvQuery
{
    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function csv(array $query): array
    {
        foreach ($query as $k => $v) {
            if (is_array($v)) {
                $query[$k] = implode(',', $v);
            } elseif (is_bool($v)) {
                $query[$k] = $v ? 'true' : 'false';
            } elseif ($v === null) {
                unset($query[$k]);
            }
        }

        return $query;
    }
}
