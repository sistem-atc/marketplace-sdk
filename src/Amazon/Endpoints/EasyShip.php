<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Easy Ship API 2022-03-23 (path /easyShip/2022-03-23) — agendamento de
 * coleta/entrega Amazon Easy Ship pra pedidos MFN.
 *
 * Rate limit (modelo): 1 req/s + burst 5 em todas as operações.
 *
 * Respostas vêm no nível raiz (sem `payload`).
 */
class EasyShip
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Janelas de handover disponíveis pra um pedido
     * (POST /easyShip/2022-03-23/timeSlot). Retorno em `timeSlots[]`.
     *
     * @param  array<string, mixed>  $body  ListHandoverSlotsRequest (marketplaceId, amazonOrderId, packageDimensions, packageWeight)
     */
    public function listHandoverSlots(array $body = []): array
    {
        return $this->client->post('/easyShip/2022-03-23/timeSlot', $body);
    }

    /**
     * Pacote agendado de um pedido (GET /easyShip/2022-03-23/package).
     * Retorno = Package (scheduledPackageId, packageDimensions, packageTimeSlot, ...).
     */
    public function getScheduledPackage(string $amazonOrderId, string $marketplaceId): array
    {
        return $this->client->get('/easyShip/2022-03-23/package', [
            'amazonOrderId' => $amazonOrderId,
            'marketplaceId' => $marketplaceId,
        ]);
    }

    /**
     * Agenda o pacote de um pedido numa janela
     * (POST /easyShip/2022-03-23/package). Retorno = Package.
     *
     * @param  array<string, mixed>  $body  CreateScheduledPackageRequest (amazonOrderId, marketplaceId, packageDetails)
     */
    public function createScheduledPackage(array $body): array
    {
        return $this->client->post('/easyShip/2022-03-23/package', $body);
    }

    /**
     * Reagenda pacotes já agendados (PATCH /easyShip/2022-03-23/package).
     * Retorno em `packages[]`.
     *
     * @param  array<string, mixed>  $body  UpdateScheduledPackagesRequest (marketplaceId, updatePackageDetailsList)
     */
    public function updateScheduledPackages(array $body = []): array
    {
        return $this->client->patch('/easyShip/2022-03-23/package', $body);
    }

    /**
     * Agenda vários pedidos de uma vez
     * (POST /easyShip/2022-03-23/packages/bulk). Retorno em
     * `scheduledPackages[]` + `rejectedOrders[]`.
     *
     * @param  array<string, mixed>  $body  CreateScheduledPackagesRequest (marketplaceId, orderScheduleDetailsList, labelFormat)
     */
    public function createScheduledPackageBulk(array $body): array
    {
        return $this->client->post('/easyShip/2022-03-23/packages/bulk', $body);
    }
}
