<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Order;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\AddExternalOrderReferencesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\ExternalOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\PodDetailsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\PriceDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Order\OrderSearchResponseDTO;
use InvalidArgumentException;

/**
 * Endpoints de pedidos TikTok Shop (OpenAPI versionada /order/{version}/...).
 *
 * Pagination: cursor via `page_token`; `next_page_token` na resposta indica
 * se ha mais. `time_ge`/`time_lt` em segundos epoch (NAO milliseconds).
 * Bulk detail: max 50 order_ids por chamada.
 */
class OrderMethods extends BaseMethods
{
    private const MAX_DETAIL_BATCH = 50;

    private const MAX_PAGE_SIZE = 100;

    private const MAX_TIME_RANGE_SECONDS = 30 * 24 * 60 * 60; // 30 dias

    private const DEFAULT_VERSION = '202309';

    /** Max de pedidos por chamada de `addExternalOrderReferences`. */
    private const MAX_EXTERNAL_REFERENCE_BATCH = 100;

    /**
     * Vocabulario FECHADO de `platform` na referencia externa. O TikTok nao
     * aceita alias livre: mandar "BUNKER" ou o nome da empresa e' recusado.
     * Pra um ERP proprio o valor correto e' `ERP_SYSTEM`.
     *
     * @var list<string>
     */
    public const EXTERNAL_ORDER_PLATFORMS = [
        'SHOPIFY',
        'WOOCOMMERCE',
        'BIGCOMMERCE',
        'MAGENTO',
        'SALESFORCE_COMMERCE_CLOUD',
        'CHANNEL_ADVISOR',
        'AMAZON',
        'ORDER_MANAGEMENT_SYSTEM',
        'WAREHOUSE_MANAGEMENT_SYSTEM',
        'ERP_SYSTEM',
    ];

    /**
     * Lista pedidos numa janela de tempo (create_time ou update_time),
     * paginada por page_token.
     *
     * @param  string  $sortField  'create_time' | 'update_time'
     * @param  string|null  $orderStatus  UNPAID | AWAITING_SHIPMENT | AWAITING_COLLECTION | IN_TRANSIT | DELIVERED | COMPLETED | CANCELLED
     */
    public function getOrderList(
        int $timeGe,
        int $timeLt,
        string $sortField = 'create_time',
        ?string $orderStatus = null,
        int $pageSize = 50,
        string $pageToken = '',
        string $version = self::DEFAULT_VERSION,
    ): OrderSearchResponseDTO {
        if ($timeLt - $timeGe > self::MAX_TIME_RANGE_SECONDS) {
            throw new InvalidArgumentException(sprintf(
                'Janela maior que %d dias (TikTok max). Use janelas menores.',
                self::MAX_TIME_RANGE_SECONDS / 86400,
            ));
        }

        $query = [
            'page_size' => min($pageSize, self::MAX_PAGE_SIZE),
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = [
            'order_status' => $orderStatus,
            $sortField.'_ge' => $timeGe,
            $sortField.'_lt' => $timeLt,
        ];
        // TikTok recusa null em filtros
        $body = array_filter($body, fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/orders/search",
            $query,
            $body,
        );

        return OrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Detalhes de pedidos por lote. Max 50 order_ids por chamada.
     *
     * @param  array<int, string>  $orderIds
     * @return list<OrderResponseDTO>
     */
    public function getOrderDetail(array $orderIds, string $version = self::DEFAULT_VERSION): array
    {
        if (empty($orderIds)) {
            return [];
        }

        if (count($orderIds) > self::MAX_DETAIL_BATCH) {
            throw new InvalidArgumentException(sprintf(
                'Max %d order_ids por chamada (recebeu %d). Fragmente o lote.',
                self::MAX_DETAIL_BATCH,
                count($orderIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/order/{$version}/orders",
            ['ids' => implode(',', $orderIds)],
        );

        return array_map(
            static fn (array $o): OrderResponseDTO => OrderResponseDTO::fromArray($o),
            $response['data']['orders'] ?? [],
        );
    }

    /**
     * Decomposicao de preco do pedido — `/order/{v}/orders/{id}/price_detail`.
     *
     * A versao e' 202407, diferente da DEFAULT_VERSION (202309) usada pelos
     * outros metodos: este endpoint so' existe a partir dela.
     *
     * Traz o desconto bancado pela PLATAFORMA separado do bancado pelo
     * VENDEDOR — numero que, sem esta chamada, so' aparecia no extrato
     * financeiro. Quando o extrato vinha vazio, o financeiro ficava com saldo
     * em aberto sem explicacao.
     */
    public function getPriceDetail(string $orderId, string $version = '202407'): ?PriceDetailResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/order/{$version}/orders/".rawurlencode($orderId).'/price_detail',
        );

        $data = $response['data'] ?? null;

        return is_array($data) ? PriceDetailResponseDTO::fromArray($data) : null;
    }

    /**
     * Grava a NOSSA referencia dentro do pedido do TikTok —
     * `POST /order/{v}/orders/external_orders`. Versao 202406 (nao existe na
     * DEFAULT_VERSION).
     *
     * POR QUE ISTO IMPORTA MAIS QUE O RESTO DESTE ARQUIVO
     * ---------------------------------------------------
     * Hoje o vinculo nota-fiscal <-> pedido do TikTok e' INFERIDO: cruzamos
     * valor e data e escolhemos o candidato mais provavel. Quando o mesmo
     * pedido tem nota duplicada (dois documentos de valor identico), a
     * heuristica erra em cerca de 70% dos casos — e o erro so' aparece na
     * conciliacao contabil, semanas depois.
     *
     * Este endpoint elimina a inferencia: grava um identificador NOSSO (a
     * chave/numero da NF-e, ou o id do pedido no ERP) dentro do proprio pedido
     * do TikTok, e `searchOrderByExternalOrderReference()` faz o caminho de
     * volta por essa chave. Deixa de ser palpite e vira lookup.
     *
     * ARMADILHAS
     * ----------
     * 1. SUCESSO PARCIAL SILENCIOSO. O envelope volta `code: 0` / "Success"
     *    mesmo quando entradas falharam: as falhas vem em
     *    `$dto->errors`, e nenhuma excecao e' lancada. Um lote de 100 pode ter
     *    gravado 3. SEMPRE inspecione `errors` antes de marcar como gravado.
     * 2. UMA referencia por `platform` por pedido. Regravar com a MESMA
     *    `platform` SOBRESCREVE a anterior; nao ha delete. Pra anexar duas
     *    origens diferentes ao mesmo pedido, chame duas vezes com `platform`
     *    diferente.
     * 3. `platform` e' vocabulario fechado (EXTERNAL_ORDER_PLATFORMS); pro
     *    nosso caso o valor e' `ERP_SYSTEM`.
     * 4. Em `line_items`, `id` e' o id da linha NO NOSSO sistema e `origin_id`
     *    o id da linha NO TIKTOK — inverter grava espelhado e a busca depois
     *    devolve vazio sem erro.
     *
     * @param  array<int, array{id: string, external_order: array{id: string, platform: string, line_items?: array<int, array{id: string, origin_id: string}>}}>  $orders
     *                                                                                                                                                            Max 100 por chamada.
     */
    public function addExternalOrderReferences(array $orders, string $version = '202406'): AddExternalOrderReferencesResponseDTO
    {
        if ($orders === []) {
            throw new InvalidArgumentException('Nenhum pedido informado para referencia externa.');
        }

        if (count($orders) > self::MAX_EXTERNAL_REFERENCE_BATCH) {
            throw new InvalidArgumentException(sprintf(
                'Max %d pedidos por chamada (recebeu %d). Fragmente o lote.',
                self::MAX_EXTERNAL_REFERENCE_BATCH,
                count($orders),
            ));
        }

        foreach ($orders as $order) {
            $platform = $order['external_order']['platform'] ?? null;

            if (! in_array($platform, self::EXTERNAL_ORDER_PLATFORMS, true)) {
                throw new InvalidArgumentException(sprintf(
                    "Platform '%s' invalida. Aceitas: %s.",
                    (string) $platform,
                    implode(', ', self::EXTERNAL_ORDER_PLATFORMS),
                ));
            }
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/orders/external_orders",
            [],
            ['orders' => array_values($orders)],
        );

        return AddExternalOrderReferencesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Caminho de volta da referencia externa —
     * `POST /order/{v}/orders/external_order_search`. Versao 202406.
     *
     * Dado o identificador que NOS gravamos (via
     * `addExternalOrderReferences()`), devolve o pedido do TikTok que o
     * carrega. E' o lookup deterministico que substitui a heuristica de
     * valor/data no vinculo nota-fiscal <-> pedido — inclusive no caso de nota
     * duplicada, onde a heuristica erra na maioria das vezes.
     *
     * QUIRKS
     * ------
     * - E' POST, mas os filtros vao na QUERY STRING (`platform`,
     *   `external_order_id`); o corpo vai vazio (`{}`). Mandar os filtros no
     *   corpo devolve resultado vazio, sem erro.
     * - Sem paginacao: a busca e' por chave e devolve o conjunto inteiro
     *   (normalmente 1 pedido).
     * - Lista VAZIA nao e' erro — significa "nenhum pedido tem essa referencia
     *   gravada", tipicamente pedido anterior ao inicio da gravacao. Nesses
     *   casos ainda e' preciso cair no metodo antigo.
     * - A busca so' acha o que foi gravado com a MESMA `platform`.
     *
     * @param  string  $platform  Um de EXTERNAL_ORDER_PLATFORMS (pro ERP: `ERP_SYSTEM`).
     * @param  string  $externalOrderId  O identificador NOSSO gravado no pedido.
     */
    public function searchOrderByExternalOrderReference(
        string $platform,
        string $externalOrderId,
        string $version = '202406',
    ): ExternalOrderSearchResponseDTO {
        if (! in_array($platform, self::EXTERNAL_ORDER_PLATFORMS, true)) {
            throw new InvalidArgumentException(sprintf(
                "Platform '%s' invalida. Aceitas: %s.",
                $platform,
                implode(', ', self::EXTERNAL_ORDER_PLATFORMS),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/orders/external_order_search",
            [
                'platform' => $platform,
                'external_order_id' => $externalOrderId,
            ],
            [],
        );

        return ExternalOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Dados de personalizacao (POD — print on demand) das linhas de um pedido
     * — `POST /order/202606/pod_details/search`.
     *
     * O detalhe do POD (o que o comprador escreveu/enviou pra gravar no
     * produto) NAO vem no `getOrderDetail()`: sem esta chamada o pedido chega
     * na expedicao sem a arte.
     *
     * A consulta e' por linha, e cada linha exige as QUATRO chaves
     * (`order_line_id`, `product_id`, `sku_id`, `pod_order_data_id`) — nao da'
     * pra pedir "o POD do pedido inteiro". `pod_order_data_id` vem do proprio
     * item do pedido.
     *
     * A versao 202606 e' exclusiva deste endpoint.
     *
     * @param  array<int, array{order_line_id: string, product_id: string, sku_id: string, pod_order_data_id: string}>  $queryOrderLines
     */
    public function getPodDetails(
        string $mainOrderId,
        array $queryOrderLines,
        string $version = '202606',
    ): PodDetailsResponseDTO {
        if ($queryOrderLines === []) {
            throw new InvalidArgumentException('Informe ao menos uma linha para consultar o POD.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/pod_details/search",
            [],
            [
                'main_order_id' => $mainOrderId,
                'query_order_lines' => array_values($queryOrderLines),
            ],
        );

        return PodDetailsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Devolve ao TikTok o resultado da abertura de caixas misteriosas
     * ("blind box") — `POST /order/202605/orders/blind_box_result/callback`.
     *
     * O vendedor e' quem sorteia o conteudo; o TikTok so' registra o que foi
     * sorteado, pra mostrar ao comprador e pro pos-venda. Enquanto o resultado
     * nao volta por aqui, o pedido fica sem o produto real e a expedicao nao
     * tem o que separar.
     *
     * DOIS FORMATOS, EXCLUDENTES NA PRATICA:
     * - `$blindBoxResults`: uma caixa por linha (1 produto sorteado por linha);
     * - `$blindBatchBoxResults`: varios produtos sorteados na MESMA linha
     *   (caixa em lote), cada um com `product_type` NORMAL (`0`) ou GIFT
     *   (`1`). Brinde entra aqui como item proprio — nao some no NORMAL.
     * Use um OU outro conforme a linha; mandar os dois vazios e' no-op.
     *
     * `blind_open_time` e' epoch em SEGUNDOS (padrao TikTok), nao milissegundos.
     *
     * `data` volta VAZIO por contrato — nao ha DTO. Devolvemos o envelope cru
     * pelo `request_id`, unico dado util (rastreio junto ao suporte do canal).
     * Falha vira excecao no `makeRequest()`; 10007002 = o pedido nao e' de
     * caixa misteriosa.
     *
     * @param  array<int, array{order_line_id: string, blind_result_product_id: string, blind_result_sku_id: string, blind_open_time: int}>  $blindBoxResults
     * @param  array<int, array{order_line_id: string, blind_box_order_line_list: array<int, array{blind_result_product_id: string, blind_result_sku_id: string, blind_open_time: int, product_type?: string}>}>  $blindBatchBoxResults
     * @return array<string, mixed> Envelope cru (`code`, `message`, `request_id`, `data` vazio).
     */
    public function updateBlindBoxOpeningResults(
        string $mainOrderId,
        array $blindBoxResults = [],
        array $blindBatchBoxResults = [],
        string $version = '202605',
    ): array {
        if ($blindBoxResults === [] && $blindBatchBoxResults === []) {
            throw new InvalidArgumentException(
                'Informe blindBoxResults ou blindBatchBoxResults — mandar os dois vazios nao atualiza nada.',
            );
        }

        $body = ['main_order_id' => $mainOrderId];

        if ($blindBoxResults !== []) {
            $body['blind_box_results'] = array_values($blindBoxResults);
        }

        if ($blindBatchBoxResults !== []) {
            $body['blind_batch_box_results'] = array_values($blindBatchBoxResults);
        }

        return $this->makeRequest(
            HttpMethod::POST,
            "/order/{$version}/orders/blind_box_result/callback",
            [],
            $body,
        );
    }
}
