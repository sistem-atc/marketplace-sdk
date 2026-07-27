<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Product Type Definitions API (2020-09-01).
 *
 * E' o equivalente Amazon do /categories/{id}/attributes do Mercado Livre —
 * com uma diferenca conceitual que importa:
 *
 *   No ML/Shopee/TikTok quem determina os atributos e' a CATEGORIA.
 *   Na Amazon quem determina e' o PRODUCT TYPE, que e' outra coisa: a
 *   categoria (browse node) organiza a vitrine; o productType define o
 *   contrato de dados do cadastro.
 *
 * E o retorno nao e' uma lista plana de atributos: e' um JSON Schema completo
 * (draft-06), com `properties`, `required`, `$defs` e enums. Quem traduz isso
 * pro formato do dicionario e' o driver no lado da aplicacao.
 */
class Definitions
{
    /** Marketplace BR na SP-API. */
    public const MARKETPLACE_BR = 'A2Q3Y263D00KWC';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista/busca os product types disponiveis.
     *
     * Sem `keywords` devolve o catalogo inteiro de tipos do marketplace — e'
     * essa lista que alimenta o Select de "tipo de produto" na tela, em vez de
     * o usuario ter que saber de cor que existe SPORTS_NUTRITION_SUPPLEMENT.
     *
     * @param  list<string>  $keywords  filtra por palavra-chave (ex: ['supplement'])
     * @return array<string, mixed> envelope com `productTypes`
     */
    public function searchProductTypes(
        array $keywords = [],
        string $marketplaceId = self::MARKETPLACE_BR,
    ): array {
        $query = ['marketplaceIds' => $marketplaceId];

        if ($keywords !== []) {
            $query['keywords'] = implode(',', $keywords);
        }

        return $this->client->get('/definitions/2020-09-01/productTypes', $query);
    }

    /**
     * Schema COMPLETO de um product type.
     *
     * `requirements`:
     *   LISTING              — tudo que a oferta exige (o caso de uso daqui)
     *   LISTING_PRODUCT_ONLY — so' os dados de produto, sem os de oferta
     *   LISTING_OFFER_ONLY   — so' os de oferta
     *
     * O schema em si vem por LINK (`schema.link.resource`), nao embutido na
     * resposta — a Amazon devolve uma URL assinada e temporaria. Use
     * `fetchSchema()` pra baixar o conteudo.
     *
     * @return array<string, mixed>
     */
    public function getProductType(
        string $productType,
        string $marketplaceId = self::MARKETPLACE_BR,
        string $requirements = 'LISTING',
        string $locale = 'pt_BR',
    ): array {
        return $this->client->get(
            '/definitions/2020-09-01/productTypes/'.rawurlencode($productType),
            [
                'marketplaceIds' => $marketplaceId,
                'requirements' => $requirements,
                'locale' => $locale,
            ],
        );
    }

    /**
     * Baixa o JSON Schema apontado por `getProductType()`.
     *
     * A URL e' assinada, temporaria e NAO leva credencial da SP-API — por isso
     * a chamada e' um GET simples, fora do client autenticado. Passar o token
     * da Amazon pra um host de conteudo seria vazamento gratuito.
     *
     * @param  array<string, mixed>  $definition  retorno de getProductType()
     * @return array<string, mixed> o JSON Schema (vazio se o link nao veio)
     */
    public function fetchSchema(array $definition): array
    {
        $url = $definition['schema']['link']['resource'] ?? null;

        if (! is_string($url) || $url === '') {
            return [];
        }

        $contents = @file_get_contents($url);

        if ($contents === false) {
            return [];
        }

        return json_decode($contents, true) ?: [];
    }
}
