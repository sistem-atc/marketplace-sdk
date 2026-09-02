<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Bundle;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

/**
 * Agrupamento de pacotes para a Coleta (/soe/bundles).
 *
 * Um bundle e uma "unidade contenerizada" (saco/caixa) com N volumes
 * (shipments) de logistica XD. Operacoes de volume exigem o `hash` corrente do
 * bundle (vem em list()/create()) — hash velho = 4xx. Status: OPENED, CLOSED,
 * DELETED. Antes de usar, validateUser() precisa devolver active=true.
 */
class BundleMethods extends BaseMethods
{
    /**
     * Vendedor habilitado no agrupamento? (GET /soe/bundles/users/validate):
     * {active: bool, init_date?} ou {active: false, reason: FEATURE_DISABLED}.
     *
     * @return array<string,mixed>
     */
    public function validateUser(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/soe/bundles/users/validate');
    }

    /**
     * Cria bundle (POST /soe/bundles, body {name, volumes?[]}). `name` ate 15
     * chars; volumes = ids de shipment (string) em ready_to_ship/printed.
     * Resposta traz id, status OPENED, service_type XD, hash e volumes[].
     *
     * @param  list<int|string>  $volumes
     * @return array<string,mixed>
     */
    public function create(string $name, array $volumes = []): array
    {
        $body = ['name' => $name];
        if ($volumes !== []) $body['volumes'] = array_map('strval', $volumes);

        return $this->makeRequest(HttpMethod::POST, '/soe/bundles', body: $body);
    }

    /**
     * Todos os bundles do vendedor (GET /soe/bundles): abertos, fechados,
     * finalizados e cancelados, SEM volumes (use volumes()/summary()).
     *
     * @return array<int, array<string,mixed>>
     */
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/soe/bundles');
    }

    /**
     * Busca bundles por referencia parcial — nome ou codigo bipavel
     * (GET /soe/bundles/search?bundle_reference=). Vazio = 400; nada achado = 404.
     *
     * @return array<string,mixed>
     */
    public function search(string $bundleReference): array
    {
        return $this->makeRequest(HttpMethod::GET, '/soe/bundles/search', ['bundle_reference' => $bundleReference]);
    }

    /**
     * Resumo do bundle + volumes (GET /soe/bundles/{id}/summary).
     *
     * @return array<string,mixed>
     */
    public function summary(int|string $bundleId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/soe/bundles/{$bundleId}/summary");
    }

    /**
     * Renomeia e/ou muda status do bundle (PUT /soe/bundles/{id}, body {name?, status?}).
     * status aceito: CLOSED | DELETED. DELETED devolve corpo vazio.
     *
     * @return array<string,mixed>
     */
    public function update(int|string $bundleId, ?string $name = null, ?string $status = null): array
    {
        $body = array_filter(['name' => $name, 'status' => $status], fn ($v) => $v !== null);

        return $this->makeRequest(HttpMethod::PUT, "/soe/bundles/{$bundleId}", body: $body);
    }

    /**
     * Volumes (shipments) do bundle (GET /soe/bundles/{id}/volumes).
     *
     * @return array<string,mixed>
     */
    public function volumes(int|string $bundleId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/soe/bundles/{$bundleId}/volumes");
    }

    /**
     * Busca volume dentro do bundle por parte do shipment_id
     * (GET /soe/bundles/{id}/volumes/search?volume_reference=).
     *
     * @return array<string,mixed>
     */
    public function searchVolumes(int|string $bundleId, string $volumeReference): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/soe/bundles/{$bundleId}/volumes/search",
            ['volume_reference' => $volumeReference],
        );
    }

    /**
     * Adiciona volumes ao bundle (PUT /soe/bundles/{id}/volumes, body {volumes[], hash}).
     * 200 vazio no sucesso; erro de negocio vem como 200 com {status, reason:
     * ERR_INVALID_BUNDLE_STATUS|ERR_BUNDLE_IS_DELETED|..., max_limit} — cheque
     * `reason` no retorno.
     *
     * @param  list<int|string>  $volumes
     * @return array<string,mixed>
     */
    public function addVolumes(int|string $bundleId, array $volumes, string $hash): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/soe/bundles/{$bundleId}/volumes",
            body: ['volumes' => array_map('strval', $volumes), 'hash' => $hash],
        );
    }

    /**
     * Remove volumes do bundle (DELETE /soe/bundles/{id}/volumes, body {volumes[], hash}).
     * DELETE COM body JSON — nao mover pra query.
     *
     * @param  list<int|string>  $volumes
     * @return array<string,mixed>
     */
    public function removeVolumes(int|string $bundleId, array $volumes, string $hash): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/soe/bundles/{$bundleId}/volumes",
            body: ['volumes' => array_map('strval', $volumes), 'hash' => $hash],
        );
    }

    /**
     * Arquivo CSV do bundle (GET /soe/bundles/{id}/file) — corpo BINARIO cru,
     * por isso nao passa pelo makeRequest. Content-Type/Disposition vem nos
     * headers da resposta.
     */
    public function downloadFile(int|string $bundleId): string
    {
        $response = $this->httpClient->get("/soe/bundles/{$bundleId}/file");
        if ($response->failed()) throw new MercadoLivreRequestException($response);

        return $response->body();
    }

    /**
     * Etiqueta do bundle (POST /soe/bundles/label, body {bundle_id, format?}).
     * format: pdf | zpl; null = preferencia do vendedor. Resposta JSON
     * {file: base64, content_type, file_name} — decodifique `file`.
     *
     * @return array<string,mixed>
     */
    public function label(int|string $bundleId, ?string $format = null): array
    {
        $body = ['bundle_id' => $bundleId];
        if ($format !== null) $body['format'] = $format;

        return $this->makeRequest(HttpMethod::POST, '/soe/bundles/label', body: $body);
    }
}
