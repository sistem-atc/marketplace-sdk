<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Moderation;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Moderações de anúncios: infrações do seller, detalhe da última moderação
 * e diagnóstico prévio de imagens.
 */
class ModerationMethods extends BaseMethods
{
    /**
     * Detalhe da última moderação de um elemento
     * (GET /moderations/last_moderation/{reference}). A referência é
     * `{element_id}-{element_type}` (ex.: MLB123-ITM); passe o item id e o
     * tipo separados e o método monta. Devolve lista de moderações com
     * evidences (seção + texto) e wordings (REASON/REMEDY).
     */
    public function lastModeration(string $elementId, string $elementType = 'ITM'): array
    {
        $reference = str_contains($elementId, '-') ? $elementId : "{$elementId}-{$elementType}";

        return $this->makeRequest(HttpMethod::GET, '/moderations/last_moderation/'.rawurlencode($reference));
    }

    /**
     * Infrações do seller (GET /moderations/infractions/{user}). Filtros:
     * element_type (ITM|REV|QUE), date_created_since (YYYY-MM-DD), limit,
     * offset. Devolve {infractions[{id, related_item_id, element_id,
     * filter_subgroup, reason, remedy}], paging, sorting_type}.
     *
     * @param  array<string,mixed>  $query
     */
    public function infractions(int|string $userId, array $query = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/moderations/infractions/{$userId}", $query);
    }

    /**
     * Diagnóstico de uma imagem antes de publicar
     * (POST /moderations/pictures/diagnostic). `$picture` aceita URL, base64
     * ou picture_id do ML; picture_type: thumbnail|variation_thumbnail|other.
     * Devolve {id, diagnostics[{picture_type, action, detections[{name, wordings}]}]}.
     */
    public function pictureDiagnostic(string $picture, string $categoryId, string $title, string $pictureType = 'thumbnail'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/moderations/pictures/diagnostic', [], [
            'picture_url' => $picture,
            'context' => [
                'category_id' => $categoryId,
                'title' => $title,
                'picture_type' => $pictureType,
            ],
        ]);
    }
}
