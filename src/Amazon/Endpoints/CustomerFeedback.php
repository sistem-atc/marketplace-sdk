<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Customer Feedback API 2024-06-01 — tópicos e tendências de avaliações e
 * devoluções, por ASIN ou por browse node (categoria). Só marketplaces onde a
 * Amazon disponibiliza (a resposta vem com `dateRange` da janela analisada).
 * Rate limits não publicados no modelo.
 */
class CustomerFeedback
{
    private const BASE = '/customerFeedback/2024-06-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Tópicos mais citados nas avaliações do ASIN
     * (GET …/items/{asin}/reviews/topics). `sortBy`: MENTIONS | STAR_RATING_IMPACT.
     * Retorna {asin, itemName, marketplaceId, countryCode, dateRange, topics[]}.
     *
     * @return array<string, mixed>
     */
    public function getItemReviewTopics(string $asin, string $marketplaceId, string $sortBy = 'MENTIONS'): array
    {
        return $this->client->get(
            self::BASE.'/items/'.rawurlencode($asin).'/reviews/topics',
            ['marketplaceId' => $marketplaceId, 'sortBy' => $sortBy],
        );
    }

    /**
     * Browse node (categoria) do ASIN (GET …/items/{asin}/browseNode) →
     * {browseNodeId, displayName}. Use o id nos métodos por browse node.
     *
     * @return array<string, mixed>
     */
    public function getItemBrowseNode(string $asin, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/items/'.rawurlencode($asin).'/browseNode',
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Tópicos das avaliações da CATEGORIA
     * (GET …/browseNodes/{browseNodeId}/reviews/topics). `sortBy`: MENTIONS |
     * STAR_RATING_IMPACT.
     *
     * @return array<string, mixed>
     */
    public function getBrowseNodeReviewTopics(string $browseNodeId, string $marketplaceId, string $sortBy = 'MENTIONS'): array
    {
        return $this->client->get(
            self::BASE.'/browseNodes/'.rawurlencode($browseNodeId).'/reviews/topics',
            ['marketplaceId' => $marketplaceId, 'sortBy' => $sortBy],
        );
    }

    /**
     * Tendência das avaliações do ASIN ao longo do tempo
     * (GET …/items/{asin}/reviews/trends) → `reviewTrends[]`.
     *
     * @return array<string, mixed>
     */
    public function getItemReviewTrends(string $asin, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/items/'.rawurlencode($asin).'/reviews/trends',
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Tendência das avaliações da CATEGORIA
     * (GET …/browseNodes/{browseNodeId}/reviews/trends) → `reviewTrends[]`.
     *
     * @return array<string, mixed>
     */
    public function getBrowseNodeReviewTrends(string $browseNodeId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/browseNodes/'.rawurlencode($browseNodeId).'/reviews/trends',
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Motivos de DEVOLUÇÃO mais citados na categoria
     * (GET …/browseNodes/{browseNodeId}/returns/topics) → `topics[]`.
     *
     * @return array<string, mixed>
     */
    public function getBrowseNodeReturnTopics(string $browseNodeId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/browseNodes/'.rawurlencode($browseNodeId).'/returns/topics',
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Tendência de devoluções da categoria
     * (GET …/browseNodes/{browseNodeId}/returns/trends) → `returnTrends[]`.
     *
     * @return array<string, mixed>
     */
    public function getBrowseNodeReturnTrends(string $browseNodeId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/browseNodes/'.rawurlencode($browseNodeId).'/returns/trends',
            ['marketplaceId' => $marketplaceId],
        );
    }
}
