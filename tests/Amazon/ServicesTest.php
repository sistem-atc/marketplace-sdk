<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Endpoints\Services;
use SistemAtc\Marketplaces\Tests\Support\FakeIntegration;

const SVC = 'https://sellingpartnerapi-na.amazon.com/service/v1';

beforeEach(function () {
    Http::preventStrayRequests();
});

function servicesEndpoint(): Services
{
    $integration = new FakeIntegration(
        accessToken: 'Atza|valid-token',
        refreshToken: 'Atzr|refresh',
        settings: ['client_id' => 'test-client', 'client_secret' => 'test-secret', 'marketplace_id' => 'A2Q3Y263D00KWC'],
        active: true,
        expired: false,
    );

    return new Services(new Client($integration));
}

function servicesAssertSent(string $method, string $url, ?array $body = null): void
{
    Http::assertSent(function (Request $req) use ($method, $url, $body): bool {
        if ($req->method() !== $method || $req->url() !== $url) {
            return false;
        }
        if ($req->header('x-amz-access-token')[0] !== 'Atza|valid-token') {
            return false;
        }
        if ($body !== null && $req->data() !== $body) {
            return false;
        }

        return true;
    });
}

it('getServiceJobByServiceJobId GET /serviceJobs/{id}', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1' => Http::response(['payload' => ['serviceJobId' => 'SJ-1']])]);
    expect(servicesEndpoint()->getServiceJobByServiceJobId('SJ-1')['payload']['serviceJobId'])->toBe('SJ-1');
    servicesAssertSent('GET', SVC.'/serviceJobs/SJ-1');
});

it('cancelServiceJobByServiceJobId PUT /cancellations com cancellationReasonCode na query', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/cancellations*' => Http::response(['errors' => []])]);
    servicesEndpoint()->cancelServiceJobByServiceJobId('SJ-1', 'CUSTOMER_REQUEST');
    servicesAssertSent('PUT', SVC.'/serviceJobs/SJ-1/cancellations?cancellationReasonCode=CUSTOMER_REQUEST');
});

it('completeServiceJobByServiceJobId PUT /completions', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/completions' => Http::response(['errors' => []])]);
    servicesEndpoint()->completeServiceJobByServiceJobId('SJ-1');
    servicesAssertSent('PUT', SVC.'/serviceJobs/SJ-1/completions');
});

it('getServiceJobs GET /serviceJobs com marketplaceIds e filtros csv', function () {
    Http::fake([SVC.'/serviceJobs*' => Http::response(['payload' => ['jobs' => [], 'nextPageToken' => 'N2']])]);
    $resp = servicesEndpoint()->getServiceJobs(['A2Q3Y263D00KWC'], [
        'serviceJobStatus' => ['PENDING_SCHEDULE', 'HOLD'],
        'createdAfter' => '2026-08-01T00:00:00Z',
        'pageSize' => 20,
    ]);
    expect($resp['payload']['nextPageToken'])->toBe('N2');
    servicesAssertSent('GET', SVC.'/serviceJobs?'.http_build_query([
        'marketplaceIds' => 'A2Q3Y263D00KWC',
        'serviceJobStatus' => 'PENDING_SCHEDULE,HOLD',
        'createdAfter' => '2026-08-01T00:00:00Z',
        'pageSize' => 20,
    ]));
});

it('addAppointmentForServiceJobByServiceJobId POST /appointments com body', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/appointments' => Http::response(['appointmentId' => 'AP-1'])]);
    $body = ['appointmentTime' => ['startTime' => '2026-09-10T13:00:00Z', 'durationInMinutes' => 60]];
    expect(servicesEndpoint()->addAppointmentForServiceJobByServiceJobId('SJ-1', $body)['appointmentId'])->toBe('AP-1');
    servicesAssertSent('POST', SVC.'/serviceJobs/SJ-1/appointments', $body);
});

it('rescheduleAppointmentForServiceJobByServiceJobId POST /appointments/{appointmentId}', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/appointments/AP-1' => Http::response(['appointmentId' => 'AP-2'])]);
    $body = ['appointmentTime' => ['startTime' => '2026-09-11T13:00:00Z', 'durationInMinutes' => 60], 'rescheduleReasonCode' => 'R1'];
    servicesEndpoint()->rescheduleAppointmentForServiceJobByServiceJobId('SJ-1', 'AP-1', $body);
    servicesAssertSent('POST', SVC.'/serviceJobs/SJ-1/appointments/AP-1', $body);
});

it('assignAppointmentResources PUT /appointments/{appointmentId}/resources', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/appointments/AP-1/resources' => Http::response(['payload' => ['warnings' => []]])]);
    $body = ['resources' => [['resourceId' => 'RES-1']]];
    servicesEndpoint()->assignAppointmentResources('SJ-1', 'AP-1', $body);
    servicesAssertSent('PUT', SVC.'/serviceJobs/SJ-1/appointments/AP-1/resources', $body);
});

it('setAppointmentFulfillmentData PUT /appointments/{appointmentId}/fulfillment', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/appointments/AP-1/fulfillment' => Http::response('', 204)]);
    $body = ['fulfillmentTime' => ['startTime' => '2026-09-10T13:00:00Z', 'endTime' => '2026-09-10T14:00:00Z']];
    expect(servicesEndpoint()->setAppointmentFulfillmentData('SJ-1', 'AP-1', $body))->toBe([]);
    servicesAssertSent('PUT', SVC.'/serviceJobs/SJ-1/appointments/AP-1/fulfillment', $body);
});

it('getRangeSlotCapacity POST /capacity/range com marketplaceIds + nextPageToken na query', function () {
    Http::fake([SVC.'/serviceResources/RES-1/capacity/range*' => Http::response(['resourceId' => 'RES-1', 'capacities' => []])]);
    $body = ['startDateTime' => '2026-09-10T00:00:00Z', 'endDateTime' => '2026-09-11T00:00:00Z', 'capacityTypes' => ['AVAILABLE_CAPACITY']];
    servicesEndpoint()->getRangeSlotCapacity('RES-1', ['A2Q3Y263D00KWC'], $body, 'T2');
    servicesAssertSent('POST', SVC.'/serviceResources/RES-1/capacity/range?marketplaceIds=A2Q3Y263D00KWC&nextPageToken=T2', $body);
});

it('getFixedSlotCapacity POST /capacity/fixed sem nextPageToken', function () {
    Http::fake([SVC.'/serviceResources/RES-1/capacity/fixed*' => Http::response(['resourceId' => 'RES-1', 'slotDuration' => 30])]);
    $body = ['startDateTime' => '2026-09-10T00:00:00Z', 'endDateTime' => '2026-09-11T00:00:00Z', 'capacityTypes' => ['SCHEDULED_CAPACITY'], 'slotDuration' => 30];
    servicesEndpoint()->getFixedSlotCapacity('RES-1', ['A2Q3Y263D00KWC'], $body);
    servicesAssertSent('POST', SVC.'/serviceResources/RES-1/capacity/fixed?marketplaceIds=A2Q3Y263D00KWC', $body);
});

it('updateSchedule PUT /serviceResources/{id}/schedules com marketplaceIds na query', function () {
    Http::fake([SVC.'/serviceResources/RES-1/schedules*' => Http::response(['payload' => []])]);
    $body = ['schedules' => [['startTime' => '2026-09-10T08:00:00Z', 'endTime' => '2026-09-10T18:00:00Z']]];
    servicesEndpoint()->updateSchedule('RES-1', ['A2Q3Y263D00KWC'], $body);
    servicesAssertSent('PUT', SVC.'/serviceResources/RES-1/schedules?marketplaceIds=A2Q3Y263D00KWC', $body);
});

it('createReservation POST /reservation com marketplaceIds na query', function () {
    Http::fake([SVC.'/reservation*' => Http::response(['payload' => ['reservationId' => 'RV-1']])]);
    $body = ['resourceId' => 'RES-1', 'reservation' => ['startTime' => '2026-09-10T08:00:00Z', 'endTime' => '2026-09-10T09:00:00Z', 'availabilityType' => 'FIXED_SLOT']];
    expect(servicesEndpoint()->createReservation(['A2Q3Y263D00KWC'], $body)['payload']['reservationId'])->toBe('RV-1');
    servicesAssertSent('POST', SVC.'/reservation?marketplaceIds=A2Q3Y263D00KWC', $body);
});

it('updateReservation PUT /reservation/{id}', function () {
    Http::fake([SVC.'/reservation/RV-1*' => Http::response(['payload' => ['reservationId' => 'RV-1']])]);
    $body = ['resourceId' => 'RES-1', 'reservation' => ['startTime' => '2026-09-10T10:00:00Z', 'endTime' => '2026-09-10T11:00:00Z']];
    servicesEndpoint()->updateReservation('RV-1', ['A2Q3Y263D00KWC'], $body);
    servicesAssertSent('PUT', SVC.'/reservation/RV-1?marketplaceIds=A2Q3Y263D00KWC', $body);
});

it('cancelReservation DELETE /reservation/{id} com marketplaceIds na query', function () {
    Http::fake([SVC.'/reservation/RV-1*' => Http::response('', 204)]);
    expect(servicesEndpoint()->cancelReservation('RV-1', ['A2Q3Y263D00KWC']))->toBe([]);
    servicesAssertSent('DELETE', SVC.'/reservation/RV-1?marketplaceIds=A2Q3Y263D00KWC');
});

it('getAppointmmentSlotsByJobId GET /serviceJobs/{id}/appointmentSlots', function () {
    Http::fake([SVC.'/serviceJobs/SJ-1/appointmentSlots*' => Http::response(['payload' => ['appointmentSlots' => []]])]);
    servicesEndpoint()->getAppointmmentSlotsByJobId('SJ-1', ['A2Q3Y263D00KWC'], ['startTime' => '2026-09-10T00:00:00Z']);
    servicesAssertSent('GET', SVC.'/serviceJobs/SJ-1/appointmentSlots?marketplaceIds=A2Q3Y263D00KWC&startTime='.rawurlencode('2026-09-10T00:00:00Z'));
});

it('getAppointmentSlots GET /appointmentSlots com asin + storeId + marketplaceIds', function () {
    Http::fake([SVC.'/appointmentSlots*' => Http::response(['payload' => ['schedulingType' => 'REAL_TIME_SCHEDULING']])]);
    servicesEndpoint()->getAppointmentSlots('B0TEST', 'STORE-1', ['A2Q3Y263D00KWC']);
    servicesAssertSent('GET', SVC.'/appointmentSlots?asin=B0TEST&storeId=STORE-1&marketplaceIds=A2Q3Y263D00KWC');
});

it('createServiceDocumentUploadDestination POST /documents com contentType + contentLength', function () {
    Http::fake([SVC.'/documents' => Http::response(['payload' => ['uploadDestinationId' => 'UD-1', 'url' => 'https://s3.example/put']])]);
    $resp = servicesEndpoint()->createServiceDocumentUploadDestination('PDF', 1234);
    expect($resp['payload']['uploadDestinationId'])->toBe('UD-1');
    servicesAssertSent('POST', SVC.'/documents', ['contentType' => 'PDF', 'contentLength' => 1234]);
});

it('uploadServiceDocument PUT binario na URL pre-assinada sem token SP-API, repetindo headers do destino', function () {
    Http::fake(['https://s3.example/put' => Http::response('', 200)]);
    expect(servicesEndpoint()->uploadServiceDocument('https://s3.example/put', '%PDF-1.4', 'application/pdf', ['x-amz-meta-foo' => 'bar']))->toBeTrue();
    Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
        && $r->url() === 'https://s3.example/put'
        && $r->body() === '%PDF-1.4'
        && $r->header('Content-Type')[0] === 'application/pdf'
        && $r->header('x-amz-meta-foo')[0] === 'bar'
        && ! $r->hasHeader('x-amz-access-token'));
});
