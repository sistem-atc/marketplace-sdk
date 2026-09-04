<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Services API v1 (Amazon Home Services) — service jobs (ordens de serviço),
 * appointments (agendamentos), capacidade/agenda de recursos, reservas e
 * upload de documento (POA — proof of appointment).
 *
 * Respostas v1 embrulham o dado em `payload` (getServiceJobs, getServiceJob,
 * appointment slots, upload destination); capacidade/reserva devolvem o
 * objeto na raiz. Erros vêm em `errors[]`. Paginação por `pageToken`
 * (getServiceJobs → `payload.nextPageToken`) ou `nextPageToken`
 * (capacidade). Arrays de query viram csv.
 *
 * Só faz sentido pra seller cadastrado como provedor de serviços (role
 * "Amazon Home Services"); não se aplica ao nosso catálogo de produtos.
 */
class Services
{
    use FlattensCsvQuery;

    private const BASE = '/service/v1';

    public function __construct(
        private readonly Client $client,
    ) {}

    // -------------------------------------------------------------------
    // Service jobs
    // -------------------------------------------------------------------

    /**
     * Detalhe de um service job (GET /service/v1/serviceJobs/{serviceJobId}).
     * Dado em `payload`. Rate limit: 20 req/s + burst 40.
     *
     * @return array<string, mixed>
     */
    public function getServiceJobByServiceJobId(string $serviceJobId): array
    {
        return $this->client->get(self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId));
    }

    /**
     * Cancela um service job (PUT /service/v1/serviceJobs/{serviceJobId}/cancellations).
     * `cancellationReasonCode` obrigatório na query. Rate limit: 5 req/s + burst 20.
     *
     * @return array<string, mixed>
     */
    public function cancelServiceJobByServiceJobId(string $serviceJobId, string $cancellationReasonCode): array
    {
        $path = self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/cancellations'
            .'?'.http_build_query(['cancellationReasonCode' => $cancellationReasonCode]);

        return $this->client->put($path);
    }

    /**
     * Marca um service job como concluído (PUT /service/v1/serviceJobs/{serviceJobId}/completions).
     * Rate limit: 5 req/s + burst 20.
     *
     * @return array<string, mixed>
     */
    public function completeServiceJobByServiceJobId(string $serviceJobId): array
    {
        return $this->client->put(self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/completions');
    }

    /**
     * Lista service jobs (GET /service/v1/serviceJobs). `marketplaceIds`
     * obrigatório. Query opcional: serviceOrderIds[], productOrderIds[],
     * trackingIds[], serviceJobStatus[] (NOT_SERVICED|CANCELLED|COMPLETED|
     * PENDING_SCHEDULE|NOT_FULFILLABLE|HOLD|PAYMENT_DECLINED), pageToken,
     * pageSize (1–20), sortField, sortOrder, createdAfter/createdBefore ou
     * lastUpdatedAfter/lastUpdatedBefore (um dos dois "After" é obrigatório),
     * scheduleStartDate/scheduleEndDate, asins[], requiredSkills[], storeIds[].
     * Dado em `payload.jobs[]` + `payload.nextPageToken`. Rate limit: 10 req/s + burst 40.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getServiceJobs(array $marketplaceIds, array $query = []): array
    {
        return $this->client->get(self::BASE.'/serviceJobs', $this->csv(['marketplaceIds' => $marketplaceIds] + $query));
    }

    // -------------------------------------------------------------------
    // Appointments
    // -------------------------------------------------------------------

    /**
     * Adiciona um appointment ao service job
     * (POST /service/v1/serviceJobs/{serviceJobId}/appointments). Body
     * AddAppointmentRequest: `appointmentTime` {startTime, durationInMinutes}.
     * Retorna `appointmentId` + `warnings[]`. Rate limit: 5 req/s + burst 20.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function addAppointmentForServiceJobByServiceJobId(string $serviceJobId, array $body): array
    {
        return $this->client->post(self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/appointments', $body);
    }

    /**
     * Reagenda um appointment
     * (POST /service/v1/serviceJobs/{serviceJobId}/appointments/{appointmentId}).
     * Body RescheduleAppointmentRequest: `appointmentTime` + `rescheduleReasonCode`.
     * Rate limit: 5 req/s + burst 20.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function rescheduleAppointmentForServiceJobByServiceJobId(string $serviceJobId, string $appointmentId, array $body): array
    {
        return $this->client->post(
            self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/appointments/'.rawurlencode($appointmentId),
            $body,
        );
    }

    /**
     * Atribui/sobrescreve os recursos (técnicos) de um appointment
     * (PUT /service/v1/serviceJobs/{serviceJobId}/appointments/{appointmentId}/resources).
     * Body: `resources[]` [{resourceId}]. Rate limit: 1 req/s + burst 2.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function assignAppointmentResources(string $serviceJobId, string $appointmentId, array $body): array
    {
        return $this->client->put(
            self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/appointments/'.rawurlencode($appointmentId).'/resources',
            $body,
        );
    }

    /**
     * Registra os dados de execução de um appointment (horário real, documentos
     * de POA enviados via createServiceDocumentUploadDestination)
     * (PUT /service/v1/serviceJobs/{serviceJobId}/appointments/{appointmentId}/fulfillment).
     * Body SetAppointmentFulfillmentDataRequest: estimatedArrivalTime,
     * fulfillmentTime {startTime, endTime}, appointmentResources[],
     * fulfillmentDocuments[] [{uploadDestinationId, contentSha256}].
     * Resposta 204 vazia. Rate limit: 5 req/s + burst 20.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function setAppointmentFulfillmentData(string $serviceJobId, string $appointmentId, array $body): array
    {
        return $this->client->put(
            self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/appointments/'.rawurlencode($appointmentId).'/fulfillment',
            $body,
        );
    }

    // -------------------------------------------------------------------
    // Capacidade / agenda de recursos
    // -------------------------------------------------------------------

    /**
     * Capacidade de um recurso em janelas livres (POST
     * /service/v1/serviceResources/{resourceId}/capacity/range). Body
     * RangeSlotCapacityQuery: `startDateTime`, `endDateTime`, `capacityTypes[]`
     * (SCHEDULED_CAPACITY|AVAILABLE_CAPACITY|ENCUMBERED_CAPACITY|RESERVED_CAPACITY).
     * Retorna na raiz `resourceId`, `capacities[]`, `nextPageToken`
     * (passar em `nextPageToken`). Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function getRangeSlotCapacity(string $resourceId, array $marketplaceIds, array $body, ?string $nextPageToken = null): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds, 'nextPageToken' => $nextPageToken]);

        return $this->client->post(
            self::BASE.'/serviceResources/'.rawurlencode($resourceId).'/capacity/range?'.http_build_query($query),
            $body,
        );
    }

    /**
     * Capacidade de um recurso em slots de tamanho fixo (POST
     * /service/v1/serviceResources/{resourceId}/capacity/fixed). Body
     * FixedSlotCapacityQuery: `startDateTime`, `endDateTime`, `capacityTypes[]`,
     * `slotDuration` (múltiplo de 5 min). Retorna na raiz `resourceId`,
     * `slotDuration`, `capacities[]`, `nextPageToken`. Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function getFixedSlotCapacity(string $resourceId, array $marketplaceIds, array $body, ?string $nextPageToken = null): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds, 'nextPageToken' => $nextPageToken]);

        return $this->client->post(
            self::BASE.'/serviceResources/'.rawurlencode($resourceId).'/capacity/fixed?'.http_build_query($query),
            $body,
        );
    }

    /**
     * Atualiza a agenda (disponibilidade) de um recurso/loja (PUT
     * /service/v1/serviceResources/{resourceId}/schedules). Body
     * UpdateScheduleRequest: `schedules[]` [{startTime, endTime, recurrence}].
     * Retorna `payload[]` (um resultado por schedule) + `errors[]`.
     * Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function updateSchedule(string $resourceId, array $marketplaceIds, array $body): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds]);

        return $this->client->put(
            self::BASE.'/serviceResources/'.rawurlencode($resourceId).'/schedules?'.http_build_query($query),
            $body,
        );
    }

    // -------------------------------------------------------------------
    // Reservas
    // -------------------------------------------------------------------

    /**
     * Cria uma reserva de capacidade (POST /service/v1/reservation). Body
     * CreateReservationRequest: `resourceId`, `reservation` {startTime,
     * endTime, recurrence?, availabilityType (FIXED_SLOT|RANGE_SLOT)}.
     * Retorna `payload` {reservationId, warnings[]}. Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createReservation(array $marketplaceIds, array $body): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds]);

        return $this->client->post(self::BASE.'/reservation?'.http_build_query($query), $body);
    }

    /**
     * Atualiza uma reserva (PUT /service/v1/reservation/{reservationId}).
     * Body UpdateReservationRequest: `resourceId`, `reservation`. Retorna
     * `payload` {reservationId, warnings[]}. Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function updateReservation(string $reservationId, array $marketplaceIds, array $body): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds]);

        return $this->client->put(
            self::BASE.'/reservation/'.rawurlencode($reservationId).'?'.http_build_query($query),
            $body,
        );
    }

    /**
     * Cancela uma reserva (DELETE /service/v1/reservation/{reservationId}).
     * Resposta 204 vazia. Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @return array<string, mixed>
     */
    public function cancelReservation(string $reservationId, array $marketplaceIds): array
    {
        $query = $this->csv(['marketplaceIds' => $marketplaceIds]);

        return $this->client->delete(
            self::BASE.'/reservation/'.rawurlencode($reservationId).'?'.http_build_query($query),
        );
    }

    // -------------------------------------------------------------------
    // Appointment slots
    // -------------------------------------------------------------------

    /**
     * Slots de agendamento disponíveis pro serviço de um job (GET
     * /service/v1/serviceJobs/{serviceJobId}/appointmentSlots). Query opcional:
     * startTime, endTime (ISO 8601; default = agora até +7 dias). Dado em
     * `payload` {schedulingType, appointmentSlots[]}. Nome com "mm" dobrado
     * vem do operationId oficial. Rate limit: 5 req/s + burst 20.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getAppointmmentSlotsByJobId(string $serviceJobId, array $marketplaceIds, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/serviceJobs/'.rawurlencode($serviceJobId).'/appointmentSlots',
            $this->csv(['marketplaceIds' => $marketplaceIds] + $query),
        );
    }

    /**
     * Slots de agendamento por contexto de serviço — ASIN + loja (GET
     * /service/v1/appointmentSlots). Query opcional: startTime, endTime.
     * Dado em `payload` {schedulingType, appointmentSlots[]}.
     * Rate limit: 20 req/s + burst 40.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getAppointmentSlots(string $asin, string $storeId, array $marketplaceIds, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/appointmentSlots',
            $this->csv(['asin' => $asin, 'storeId' => $storeId, 'marketplaceIds' => $marketplaceIds] + $query),
        );
    }

    // -------------------------------------------------------------------
    // Documentos (POA)
    // -------------------------------------------------------------------

    /**
     * Cria um destino de upload pra documento de POA (POST /service/v1/documents).
     * `contentType`: TIFF|JPG|PNG|JPEG|GIF|PDF; `contentLength` em bytes.
     * Dado em `payload` {uploadDestinationId, url, encryptionDetails, headers}.
     * Depois: uploadServiceDocument(url, conteúdo, headers) e referencie o
     * `uploadDestinationId` em setAppointmentFulfillmentData.
     * Rate limit: 5 req/s + burst 20.
     *
     * @return array<string, mixed>
     */
    public function createServiceDocumentUploadDestination(string $contentType, int $contentLength): array
    {
        return $this->client->post(self::BASE.'/documents', [
            'contentType' => $contentType,
            'contentLength' => $contentLength,
        ]);
    }

    /**
     * Sobe o binário do documento na URL pré-assinada devolvida por
     * createServiceDocumentUploadDestination (PUT direto, SEM token SP-API).
     * `$headers` = o objeto `payload.headers` do destino (a Amazon exige que
     * sejam repetidos exatamente). O modelo ainda traz `encryptionDetails`
     * (AES); a SP-API atual não exige mais criptografar o conteúdo — envie
     * o arquivo cru.
     *
     * @param  array<string, string>  $headers
     */
    public function uploadServiceDocument(string $url, string $content, string $contentType, array $headers = []): bool
    {
        return Http::timeout(120)
            ->withHeaders($headers)
            ->withBody($content, $contentType)
            ->put($url)
            ->throw()
            ->successful();
    }
}
