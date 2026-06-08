<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Billing;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingDocumentType;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingGroup;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingReportFormat;
use SistemAtc\Marketplaces\MercadoLivre\Enum\BillingReportStatus;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;
use InvalidArgumentException;

/**
 * Endpoints de Billing Reports do Mercado Livre — cobrancas que ML faz
 * pra gente como seller: taxas de venda, frete, ADS, parcelamento,
 * afiliados, e os XMLs/PDFs das NFSe emitidas pela eBazar (Osasco-SP).
 *
 * Conceitos chave:
 *   - period_key: primeiro dia do mes da fatura (ex '2026-04-01').
 *     ML disponibiliza ~5 dias uteis apos o fim do mes.
 *   - group: ML/MP (sync) ou FLEX/FULL/INSURTECH/PAYMENT (so reports async).
 *   - document_type: BILL (cobranca emitida) ou CREDIT_NOTE (estorno).
 *
 * Endpoints sincronos (cobertura BillingGroup ML + MP):
 *   GET  /billing/integration/monthly/periods                                  (atualizado 2026-04)
 *   GET  /billing/integration/periods/key/{key}/documents
 *   GET  /billing/integration/periods/key/{key}/summary/details
 *   GET  /billing/integration/periods/key/{key}/group/{ML|MP}/details          (paginacao from_id)
 *   GET  /billing/integration/periods/key/{key}/group/ML/payment/details       (NEW)
 *   GET  /billing/integration/payment/{payment_id}/charges                     (NEW)
 *   GET  /billing/integration/group/ML/order/details                           (NEW — usar com parcimonia, 429)
 *   GET  /billing/integration/legal_document/{file_id}                         (NEW — path oficial 2026-04)
 *
 * Endpoints assincronos (reports CSV/XLSX — qualquer BillingGroup):
 *   POST /billing/integration/periods/key/{key}/reports                        (NEW)
 *   GET  /billing/integration/reports/{file_id}/status                         (NEW)
 *   GET  /billing/integration/reports/{file_id}                                (NEW)
 *
 * Boa pratica oficial (doc 2026-04):
 *   - Use from_id (cursor) — NAO offset — pra paginar > 10k.
 *   - Limit max 1000 (sync) ou paginas inteiras (async reports).
 *   - Cache local. Periodos fechados nao mudam, abertos atualizam dia a dia.
 *   - Conciliacao: soma details por detail_sub_type deve bater com summary.
 *
 * Doc: https://developers.mercadolivre.com.br/pt_br/relatorios-de-faturamento
 */
class BillingMethods extends BaseMethods
{
    private const DEFAULT_LIMIT = 150;

    /** Maximo oficial da Qive 2026-04 (era 150 antes do refactor). */
    private const MAX_LIMIT = 1000;

    /**
     * Lista os ultimos 12 periodos de faturamento. Path oficial 2026-04:
     * `/billing/integration/monthly/periods` (substitui /billing/integration/periods
     * que era do schema antigo).
     *
     * @return array<string, mixed>
     */
    public function listPeriods(
        ?BillingGroup $group = null,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
        int $offset = 0,
        int $limit = 6,
    ): array {
        $query = [
            'document_type' => $documentType->value,
            'offset' => max(0, $offset),
            'limit' => min(max($limit, 1), 12),
        ];
        if ($group !== null) {
            $query['group'] = $group->value;
        }

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/billing/integration/monthly/periods',
            query: $query,
        );
    }

    /**
     * Resumo agregado do periodo (totais por categoria de cobranca e
     * bonificacao). Path oficial 2026-04: sem /group no path.
     *
     * Atencao: NAO usar em batch — uso sequencial, 1x/dia/seller. Doc
     * explicita que esse endpoint nao tolera polling agressivo.
     *
     * Estrutura da resposta:
     *   user.nickname
     *   period.{date_from, date_to, expiration_date, key}
     *   bill_includes.{total_amount, total_perceptions, bonuses[], charges[]}
     *   payment_collected.{operation_discount, total_payment, total_credit_note,
     *                      total_collected, total_debt}
     *   errors[]
     *
     * @return array<string, mixed>
     */
    public function summary(
        string $periodKey,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
    ): array {
        $this->assertPeriodKey($periodKey);

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/periods/key/{$periodKey}/summary/details",
            query: ['document_type' => $documentType->value],
        );
    }

    /**
     * Linhas detalhadas das cobrancas (charges + bonificacoes). Paginacao
     * cursor por `from_id` — recomendacao oficial pra > 10k registros.
     *
     * Filtros opcionais avancados (passar em $extra):
     *   - sort_by: 'ID' (default) | 'DATE'
     *   - order_by: 'ASC' (default) | 'DESC'
     *   - detail_type: filtra por categoria do detail
     *   - detail_sub_types: lista de sub-types separada por virgula
     *   - marketplace_type: SHIPPING / MERCADO LIVRE / MERCADO ADS / etc
     *   - order_ids: lista pra filtrar charges de orders especificos
     *   - item_ids: idem por item
     *   - document_ids: idem por document
     *
     * @param  BillingGroup  $group  ML ou MP (FLEX/FULL/INSURTECH/PAYMENT so via reports async)
     * @param  array<string, mixed>  $extra  Filtros avancados acima
     * @return array{results?: array<int, array<string, mixed>>, last_id?: string, paging?: array<string, mixed>}
     */
    public function details(
        string $periodKey,
        BillingGroup $group = BillingGroup::ML,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
        int|string|null $fromId = null,
        int $limit = self::DEFAULT_LIMIT,
        array $extra = [],
    ): array {
        $this->assertPeriodKey($periodKey);

        if (! $group->supportsSyncEndpoints()) {
            throw new InvalidArgumentException(
                "Group {$group->value} so suporta reports assincronos. Use createReport() em vez disso."
            );
        }

        $query = array_merge([
            'document_type' => $documentType->value,
            'limit' => min(max($limit, 1), self::MAX_LIMIT),
        ], $extra);

        if ($fromId !== null) {
            $query['from_id'] = (string) $fromId;
        }

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/periods/key/{$periodKey}/group/{$group->value}/details",
            query: $query,
        );
    }

    /**
     * Documentos fiscais (NFSe) emitidos no periodo. Cada item traz
     * `files[]` com refs `file_id` pra download via legalDocument().
     *
     * @return array<string, mixed>
     */
    public function documents(
        string $periodKey,
        BillingGroup $group = BillingGroup::ML,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
        ?string $documentId = null,
        int|string|null $fromId = null,
        int $limit = self::DEFAULT_LIMIT,
    ): array {
        $this->assertPeriodKey($periodKey);

        $query = [
            'group' => $group->value,
            'document_type' => $documentType->value,
            'limit' => min(max($limit, 1), self::MAX_LIMIT),
        ];
        if ($documentId !== null) {
            $query['document_id'] = $documentId;
        }
        if ($fromId !== null) {
            $query['from_id'] = (string) $fromId;
        }

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/periods/key/{$periodKey}/documents",
            query: $query,
        );
    }

    /**
     * **NEW** Detalhes de pagamentos abonados no periodo — quando ML
     * efetivamente pagou via nota de credito (NC) que abate da fatura.
     *
     * Resposta inclui pra cada payment:
     *   payment_id, payment_date, payment_amount, payment_method,
     *   amount_in_this_period, amount_in_other_period, remaining_amount,
     *   return_amount (saldo a favor do vendedor).
     *
     * IMPORTANTE: payment_id e STRING (suporta alfanumerico).
     *
     * @return array<string, mixed>
     */
    public function paymentDetails(
        string $periodKey,
        BillingGroup $group = BillingGroup::ML,
        ?string $expirationDate = null,
        string $sortBy = 'ID',
        string $orderBy = 'ASC',
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT,
    ): array {
        $this->assertPeriodKey($periodKey);

        if (! $group->supportsSyncEndpoints()) {
            throw new InvalidArgumentException(
                "Payment details so suporta group ML ou MP. Recebido: {$group->value}"
            );
        }

        $query = [
            'sort_by' => $sortBy,
            'order_by' => $orderBy,
            'offset' => max(0, min(10000, $offset)),
            'limit' => min(max($limit, 1), self::MAX_LIMIT),
        ];
        if ($expirationDate !== null) {
            $query['expiration_date'] = $expirationDate;
        }

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/periods/key/{$periodKey}/group/{$group->value}/payment/details",
            query: $query,
        );
    }

    /**
     * **NEW** Cobrancas/recebimentos vinculados a UM payment especifico.
     * Util pra entender exatamente quais charges aquele payment liquidou.
     *
     * @return array{payment_details?: array<int, array<string, mixed>>}
     */
    public function paymentCharges(
        string $paymentId,
        string $sortBy = 'ID',
        string $orderBy = 'ASC',
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT,
    ): array {
        $query = [
            'sort_by' => $sortBy,
            'order_by' => $orderBy,
            'offset' => max(0, $offset),
            'limit' => min(max($limit, 1), self::MAX_LIMIT),
        ];

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/payment/{$paymentId}/charges",
            query: $query,
        );
    }

    /**
     * **NEW** Detalhes de cobranca filtrados por order_id ou pack_id —
     * sem precisar conhecer o periodo. Util pra UI "puxar fees agora"
     * sob demanda quando o usuario abre um Order.
     *
     * ⚠️ ALTA INCIDENCIA DE 429 (doc oficial). Recomendado:
     *   - SOMENTE pra orders ainda nao processados (consulte primeiro
     *     marketplace_billing_charges.order_id no host).
     *   - Sem polling repetido — uma chamada e cacheia o resultado.
     *
     * @param  list<string>  $orderIds  Lista de orders pra filtrar (max ~50)
     * @param  list<string>  $packIds  Lista de packs (alternativa a orderIds)
     * @return array<string, mixed>
     */
    public function orderDetails(
        array $orderIds = [],
        array $packIds = [],
        BillingDocumentType $documentType = BillingDocumentType::BILL,
        int $limit = self::DEFAULT_LIMIT,
    ): array {
        if (empty($orderIds) && empty($packIds)) {
            throw new InvalidArgumentException('orderDetails requer ao menos um order_id ou pack_id.');
        }

        $query = [
            'document_type' => $documentType->value,
            'limit' => min(max($limit, 1), self::MAX_LIMIT),
        ];
        if (! empty($orderIds)) {
            $query['order_ids'] = implode(',', $orderIds);
        }
        if (! empty($packIds)) {
            $query['pack_ids'] = implode(',', $packIds);
        }

        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/billing/integration/group/ML/order/details',
            query: $query,
        );
    }

    /**
     * **NEW** Download direto de um documento legal (NFSe) por file_id.
     * Path oficial 2026-04 — bem mais simples que o caminho antigo
     * /billing/integration/periods/.../documents/{doc}/files/{file}.
     *
     * Retorna o body bruto do PDF (binario).
     *
     * Quando documents() devolve dois file_id (PDF + XML), use o file_id
     * com sufixo `_pdf` aqui.
     */
    public function legalDocument(string $fileId): string
    {
        $response = $this->httpClient->timeout(120)->get(
            "/billing/integration/legal_document/{$fileId}",
        );

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return (string) $response->body();
    }

    /**
     * Mantido por compat com o pipeline antigo de download de XML.
     * Path legado — recomenda-se migrar pra legalDocument().
     */
    public function downloadFile(
        string $periodKey,
        string $documentId,
        string $fileId,
    ): string {
        $this->assertPeriodKey($periodKey);

        $url = sprintf(
            '/billing/integration/periods/key/%s/documents/%s/files/%s',
            $periodKey,
            $documentId,
            $fileId,
        );

        $response = $this->httpClient->timeout(120)->get($url);

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return (string) $response->body();
    }

    /**
     * **NEW** Cria report assincrono (CSV ou XLSX) pra um periodo +
     * group. Aceita TODOS os groups (incluindo FLEX/FULL/INSURTECH/PAYMENT
     * que so existem aqui).
     *
     * Retorna `{fileId: "..."}` pra ser usado em `reportStatus()` +
     * `downloadReport()`.
     *
     * @return array{fileId: string}
     */
    public function createReport(
        string $periodKey,
        BillingGroup $group = BillingGroup::ML,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
        BillingReportFormat $format = BillingReportFormat::CSV,
    ): array {
        $this->assertPeriodKey($periodKey);

        // FULL e PAYMENT so suportam XLSX (doc oficial)
        if (in_array($group, [BillingGroup::FULL, BillingGroup::PAYMENT], true)
            && $format !== BillingReportFormat::XLSX) {
            throw new InvalidArgumentException(sprintf(
                'Group %s so suporta formato XLSX. Recebido: %s',
                $group->value,
                $format->value,
            ));
        }

        return $this->makeRequest(
            method: HttpMethod::POST,
            path: "/billing/integration/periods/key/{$periodKey}/reports",
            body: [
                'group' => $group->value,
                'document_type' => $documentType->value,
                'report_format' => $format->value,
            ],
        );
    }

    /**
     * **NEW** Verifica estado de criacao do report. PROCESSING/READY/ERROR.
     */
    public function reportStatus(
        string $fileId,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
    ): BillingReportStatus {
        $response = $this->makeRequest(
            method: HttpMethod::GET,
            path: "/billing/integration/reports/{$fileId}/status",
            query: ['document_type' => $documentType->value],
        );

        $status = (string) ($response['status'] ?? 'ERROR');

        return BillingReportStatus::from($status);
    }

    /**
     * **NEW** Download do report (CSV ou XLSX cru). So funciona quando
     * status = READY. Devolve body binario — caller decide gravar/parsear.
     *
     * Pra arquivos > 100MB (Soldiers PAYMENT pode chegar) ajuste
     * timeout no httpClient antes.
     */
    public function downloadReport(
        string $fileId,
        BillingDocumentType $documentType = BillingDocumentType::BILL,
    ): string {
        $response = $this->httpClient->timeout(600)->get(
            "/billing/integration/reports/{$fileId}",
            ['document_type' => $documentType->value],
        );

        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        return (string) $response->body();
    }

    private function assertPeriodKey(string $key): void
    {
        if (! preg_match('/^\d{4}-\d{2}-01$/', $key)) {
            throw new InvalidArgumentException(
                "Period key invalido: {$key}. Use YYYY-MM-01 (sempre primeiro dia do mes)."
            );
        }
    }
}
