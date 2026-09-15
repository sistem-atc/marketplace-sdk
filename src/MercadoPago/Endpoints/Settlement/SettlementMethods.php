<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Settlement;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\Enum\ReportFormat;
use SistemAtc\Marketplaces\MercadoPago\Enum\ReportStatus;
use SistemAtc\Marketplaces\MercadoPago\Exceptions\MercadoPagoRequestException;
use InvalidArgumentException;

/**
 * Endpoints de Settlement Reports / Released Money do Mercado Pago.
 *
 * **Esse e' o coracao do modulo de contas a receber.**
 *
 * MP libera o valor liquido pra conta MP do seller apos cada venda
 * (descontando taxa MP + comissao ML). Esses endpoints permitem baixar
 * o relatorio completo do periodo com cada movimento e o valor liquido
 * recebido por venda.
 *
 * Tipos de report:
 *
 *   - released_money: dinheiro liberado (entrou na conta MP do seller).
 *     PRINCIPAL pra contas a receber.
 *
 *   - account_money: movimentacoes da conta MP (entradas + saidas).
 *     Util pra conciliacao caixa MP.
 *
 *   - settlement: settlements de marketplace split payments. So usado
 *     se o seller for marketplace (recebe e split pra sub-sellers).
 *
 * Fluxo assincrono 3 passos:
 *   1) createReport(): POST cria report -> retorna `file_name`
 *   2) reportStatus(): polling ate' READY/ERROR
 *   3) downloadReport(): GET baixa CSV/XLSX
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference
 */
class SettlementMethods extends BaseMethods
{
    /**
     * Cria report assincrono de "Released Money" pro periodo.
     *
     * @param  string  $beginDate  Inicio YYYY-MM-DD
     * @param  string  $endDate  Fim YYYY-MM-DD (inclusivo)
     * @param  ReportFormat  $format  CSV (padrao) ou XLSX
     * @return array{file_name: string} Nome do arquivo pra polling subsequente
     */
    public function createReleasedMoneyReport(
        string $beginDate,
        string $endDate,
        ReportFormat $format = ReportFormat::CSV,
    ): array {
        $this->assertDate($beginDate, 'beginDate');
        $this->assertDate($endDate, 'endDate');

        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/account/release_report',
            body: [
                'begin_date' => $beginDate.'T00:00:00Z',
                'end_date' => $endDate.'T23:59:59Z',
                'file_format' => $format->value,
            ],
        );
    }

    /**
     * Cria report de Account Money (movimentacoes gerais da conta MP).
     *
     * @return array{file_name: string}
     */
    public function createAccountMoneyReport(
        string $beginDate,
        string $endDate,
        ReportFormat $format = ReportFormat::CSV,
    ): array {
        $this->assertDate($beginDate, 'beginDate');
        $this->assertDate($endDate, 'endDate');

        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/v1/account/settlement_report',
            body: [
                'begin_date' => $beginDate.'T00:00:00Z',
                'end_date' => $endDate.'T23:59:59Z',
                'file_format' => $format->value,
            ],
        );
    }

    /**
     * Colunas de que um parser de release report costuma depender.
     *
     * O relatorio MINIMO do MP traz data, valor e descricao — e nada que ligue
     * a linha ao pedido. Numa conta assim o dinheiro chega anonimo: medido em
     * 15/09/2026 numa conta real, 3.567 liberacoes gravadas e ZERO casando com
     * um pedido.
     *
     * `RECORD_TYPE` e' a mais critica e a menos obvia: sem ela nao da' pra
     * separar `release` de `initial_available_balance` / `available_balance` /
     * `total`, que sao AGREGADOS de saldo. Eles entram como se fossem movimento
     * e DOBRAM a soma.
     *
     * @var list<string>
     */
    public const COLUNAS_RECOMENDADAS = [
        'EXTERNAL_REFERENCE',
        'RECORD_TYPE',
        'ORDER_ID',
        'SHIPPING_ID',
        'INSTALLMENTS',
        'SHIPPING_FEE_AMOUNT',
        'FINANCING_FEE_AMOUNT',
        'COUPON_AMOUNT',
        'EFFECTIVE_COUPON_AMOUNT',
    ];

    /**
     * Config atual do release report da conta (colunas, idioma, frequencia).
     *
     * @return array<string, mixed>
     */
    public function releaseReportConfig(): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/v1/account/release_report/config',
        );
    }

    /**
     * Grava a config do release report.
     *
     * ## E' PUT, nao POST
     *
     * `POST` responde **409 Conflict** quando a conta ja' tem config — o
     * endpoint interno e' `report-config-legacy` e trata POST como criacao. E
     * responde **500** se o corpo trouxer `frequency` copiada de outra conta.
     * `PUT` e' o verbo de atualizacao e funciona nos dois casos.
     *
     * @param  array<string, mixed>  $config  Config COMPLETA (leia com
     *                                        `releaseReportConfig()` e altere o
     *                                        que precisa — o MP substitui tudo).
     * @return array<string, mixed>
     */
    public function saveReleaseReportConfig(array $config): array
    {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: '/v1/account/release_report/config',
            data: $config,
        );
    }

    /**
     * Acrescenta as colunas que faltam, preservando o resto da config.
     *
     * So' as COLUNAS mudam: prefixo de arquivo, frequencia e agendamento sao
     * proprios da conta, e sobrescreve-los muda o comportamento do schedule do
     * MP sem necessidade — alem de ser o que faz o POST devolver 500.
     *
     * NAO corrige o passado: o relatorio e' montado no momento do pedido, entao
     * e' preciso RE-PEDIR os periodos depois de aplicar.
     *
     * @param  list<string>|null  $colunas  Default: COLUNAS_RECOMENDADAS.
     * @return list<string> As colunas que foram acrescentadas (vazio = ja estava completa).
     */
    public function ensureReleaseReportColumns(?array $colunas = null): array
    {
        $querido = $colunas ?? self::COLUNAS_RECOMENDADAS;
        $config = $this->releaseReportConfig();

        $tem = array_map(
            static fn ($c) => is_array($c) ? (string) ($c['key'] ?? '') : (string) $c,
            $config['columns'] ?? [],
        );

        $faltam = array_values(array_diff($querido, $tem));

        if ($faltam === []) {
            return [];
        }

        foreach ($faltam as $coluna) {
            $config['columns'][] = ['key' => $coluna];
        }

        $this->saveReleaseReportConfig($config);

        return $faltam;
    }

    /**
     * Lista reports criados (released_money + settlement).
     *
     * Resposta inclui pra cada report: `file_name`, `status` (pending/
     * processing/ready/error), `begin_date`, `end_date`, `created_at`,
     * `expires_on`.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listReports(): array
    {
        $resp = $this->makeRequest(
            method: HttpMethod::GET,
            path: '/v1/account/settlement_report/list',
        );

        return $resp['results'] ?? $resp['data'] ?? $resp;
    }

    /**
     * Status de criacao de UM report especifico.
     */
    public function reportStatus(string $fileName): ReportStatus
    {
        $reports = $this->listReports();
        foreach ($reports as $r) {
            $name = $r['file_name'] ?? null;
            if ($name === $fileName) {
                $status = (string) ($r['status'] ?? 'pending');

                return ReportStatus::from(strtolower($status));
            }
        }

        // Nao listou — assume expired/error.
        return ReportStatus::EXPIRED;
    }

    /**
     * Download do report como string crua (CSV ou XLSX binario).
     *
     * Path: GET /v1/account/settlement_report/{file_name}
     *
     * Caller decide gravar em disco ou parsear inline. Pra arquivos
     * grandes preferir streaming pra arquivo + parser linha-a-linha.
     */
    public function downloadReport(string $fileName): string
    {
        $response = $this->httpClient->timeout(300)->get(
            "/v1/account/settlement_report/{$fileName}",
        );

        if ($response->failed()) {
            throw new MercadoPagoRequestException($response);
        }

        return (string) $response->body();
    }

    /**
     * Orquestra o fluxo completo 3-em-1: cria + poll + download.
     *
     * Util quando o caller precisa do CSV imediato e nao quer gerenciar
     * o ciclo assincrono. Bloqueia ate' READY ou timeout configurado.
     *
     * @return array{file_name: string, content: string, format: ReportFormat}
     */
    public function fetchReleasedMoneyReportNow(
        string $beginDate,
        string $endDate,
        ReportFormat $format = ReportFormat::CSV,
    ): array {
        $created = $this->createReleasedMoneyReport($beginDate, $endDate, $format);
        $fileName = (string) ($created['file_name'] ?? '');

        if ($fileName === '') {
            throw new \RuntimeException('Mercado Pago createReleasedMoneyReport nao retornou file_name.');
        }

        $pollInterval = (int) config('marketplaces.mercadopago.report_poll_interval', 15);
        $pollTimeout = (int) config('marketplaces.mercadopago.report_poll_timeout', 600);
        $start = time();

        while (true) {
            $status = $this->reportStatus($fileName);
            if ($status->isReady()) {
                break;
            }
            if ($status->isTerminal()) {
                throw new \RuntimeException(sprintf(
                    'Mercado Pago report %s terminou com status %s (esperado READY).',
                    $fileName,
                    $status->value,
                ));
            }
            if ((time() - $start) >= $pollTimeout) {
                throw new \RuntimeException(sprintf(
                    'Mercado Pago report %s timeout apos %ds.',
                    $fileName,
                    $pollTimeout,
                ));
            }
            if ($pollInterval > 0) {
                sleep($pollInterval);
            }
        }

        return [
            'file_name' => $fileName,
            'content' => $this->downloadReport($fileName),
            'format' => $format,
        ];
    }

    private function assertDate(string $date, string $field): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException("{$field} invalido: {$date}. Use YYYY-MM-DD.");
        }
    }
}
