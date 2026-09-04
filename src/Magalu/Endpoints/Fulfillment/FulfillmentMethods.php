<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Magalu Entregas / Fulfillment — AGENDAMENTO de envio de estoque pro CD
 * (/logistics/v1/schedules, base `services.magalu.com`).
 *
 * Fluxo: create() → upsertItems() (SKUs + custo) → availableDates() →
 * updateDate() (filial + data + tipo collect|own_delivery) → updateVolume()
 * → finalize(). Etiquetas em labels(). Endpoints marcados como PILOTO na doc.
 * Paginacao da busca e' `page`/`size` (nao `_limit`/`_offset`).
 * Escopos open:logistic-stock-scheduling-seller:read|write.
 */
class FulfillmentMethods extends BaseMethods
{
    /**
     * Busca agendamentos (GET /logistics/v1/schedules).
     *
     * Filtros: `date_start`/`date_end` (YYYY-MM-DD), `branch_ids`,
     * `schedule_ids`, `schedule_statuses` (CSV), `creation_type`
     * (NORMAL|RESERVED), `schedule_type` (collect|own_delivery).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function searchSchedules(array $filters = [], int $page = 1, int $size = 10): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl('/logistics/v1/schedules'), array_merge($filters, [
            'page' => $page,
            'size' => $size,
        ]));
    }

    /**
     * Cria agendamento vazio (POST /logistics/v1/schedules) — 201.
     * `createdBy` = UUID do usuario; `generateSuggestedProducts` pede sugestao de SKUs.
     *
     * @return array<string, mixed>
     */
    public function createSchedule(string $createdBy, ?bool $generateSuggestedProducts = null): array
    {
        $body = ['created_by' => $createdBy];
        if ($generateSuggestedProducts !== null) {
            $body['options'] = ['generate_suggested_products' => $generateSuggestedProducts];
        }

        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl('/logistics/v1/schedules'), [], $body);
    }

    /**
     * Agendamento por id (GET /logistics/v1/schedules/{id}).
     *
     * @return array<string, mixed>
     */
    public function getSchedule(string $scheduleId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}"));
    }

    /**
     * Exclui agendamento (DELETE /logistics/v1/schedules/{id}).
     *
     * @return array<string, mixed>
     */
    public function deleteSchedule(string $scheduleId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}"));
    }

    /**
     * Cancela agendamento (PUT /logistics/v1/schedules/{id}/cancel?reason=).
     *
     * @return array<string, mixed>
     */
    public function cancelSchedule(string $scheduleId, string $reason): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/cancel"), ['reason' => $reason]);
    }

    /**
     * Datas disponiveis pro tipo (GET /logistics/v1/schedules/{id}/dates/{type}).
     * `type`: collect | own_delivery.
     *
     * @return array<string, mixed>
     */
    public function availableDates(string $scheduleId, string $type): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/dates/{$type}"));
    }

    /**
     * Define filial, data/hora e tipo (PATCH /logistics/v1/schedules/{id}/date).
     *
     * @return array<string, mixed>
     */
    public function updateDate(
        string $scheduleId,
        int $branchId,
        string $date,
        string $hours,
        string $scheduleType,
        string $transportType,
    ): array {
        return $this->makeRequest(HttpMethod::PATCH, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/date"), [], [
            'branch' => ['id' => $branchId],
            'date' => $date,
            'hours' => $hours,
            'schedule' => ['type' => $scheduleType],
            'transport' => ['type' => $transportType],
        ]);
    }

    /**
     * Inclui/atualiza itens (PUT /logistics/v1/schedules/{id}/items).
     * Body e' LISTA de `{sku, quantity, cost_price}`.
     *
     * @param list<array{sku: string, quantity: int, cost_price: float|int}> $items
     * @return array<string, mixed>
     */
    public function upsertItems(string $scheduleId, array $items): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/items"), [], array_values($items));
    }

    /**
     * Remove um SKU do agendamento (DELETE /logistics/v1/schedules/{id}/item/{sku}).
     *
     * @return array<string, mixed>
     */
    public function deleteItem(string $scheduleId, string $sku): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/item/".rawurlencode($sku)));
    }

    /**
     * Volumes fisicos (PATCH /logistics/v1/schedules/{id}/volume).
     * `type`: pallet | loose_product | cardboard_box | package_bag.
     *
     * @return array<string, mixed>
     */
    public function updateVolume(string $scheduleId, int $quantity, string $type): array
    {
        return $this->makeRequest(HttpMethod::PATCH, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/volume"), [], [
            'quantity' => $quantity,
            'type' => $type,
        ]);
    }

    /**
     * Confirma o agendamento (POST /logistics/v1/schedules/{id}/finalize).
     *
     * @return array<string, mixed>
     */
    public function finalizeSchedule(string $scheduleId): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/finalize"));
    }

    /**
     * Etiquetas do agendamento (POST /logistics/v1/schedules/{id}/labels).
     *
     * Dois formatos de body conforme `type`: etiqueta de PRODUTO
     * `{type, format: pdf7x3|thermal4x2.5|thermal6x4|..., products[]}` ou de
     * VOLUME `{type, format: pdf|pdf3x6|thermal7x3|thermal10x7|..., quantity}`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function labels(string $scheduleId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl("/logistics/v1/schedules/{$scheduleId}/labels"), [], $data);
    }
}
