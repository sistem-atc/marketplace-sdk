<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Marketing;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\DiscountListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\DiscountResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Marketing\VoucherListResponseDTO;

class MarketingMethods extends BaseMethods
{
    /**
     * Lista descontos/promocoes criadas pelo vendedor.
     *
     * Modulo real da Shopee: /api/v2/discount (param discount_status,
     * paginacao por page_no 1-based). O antigo /api/v2/marketing nao existe
     * (retorna error_not_found).
     */
    public function getDiscountList(string $status, int $pageNo = 1, int $pageSize = 100): DiscountListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/discount/get_discount_list', [
            'discount_status' => $status,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return DiscountListResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Detalhes de um desconto — /api/v2/discount/get_discount. Inclui o
     * `item_list` (itens/anúncios participantes com preço promo, original e
     * estoque), paginado por `page_no` (1-based) + flag `more` na resposta.
     */
    public function getDiscount(int $discountId, int $pageNo = 1, int $pageSize = 100): DiscountResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/discount/get_discount', [
            'discount_id' => $discountId,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return DiscountResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Lista cupons (Vouchers) do vendedor — /api/v2/voucher/get_voucher_list
     * (param status, paginacao por page_no 1-based).
     */
    public function getVoucherList(string $status, int $pageNo = 1, int $pageSize = 100): VoucherListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/voucher/get_voucher_list', [
            'status' => $status,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return VoucherListResponseDTO::fromArray($response['response'] ?? []);
    }

    // ------------------------------------------------------------------
    // Discount — escrita (/api/v2/discount/*). Todos devolvem o bloco
    // `response` cru (array), sem DTO.
    // ------------------------------------------------------------------

    /**
     * Cria uma promocao de desconto da loja — /api/v2/discount/add_discount.
     * Regras da Shopee: start_time >= agora + 1h; end_time >= start_time + 1h;
     * periodo maximo de 180 dias. Devolve `discount_id`.
     *
     * @return array<string,mixed>
     */
    public function addDiscount(string $discountName, int $startTime, int $endTime): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/add_discount', [], [
            'discount_name' => $discountName,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Adiciona itens a uma promocao — /api/v2/discount/add_discount_item.
     * Cada item: item_id, purchase_limit (0 = sem limite), item_promotion_price
     * (obrigatorio se o item nao tem variacao), item_promotion_stock e
     * model_list[{model_id, model_promotion_price, model_promotion_stock}].
     * Ate 50 itens por chamada; a resposta traz `error_list` por item.
     *
     * @param  list<array<string,mixed>>  $itemList
     * @return array<string,mixed>
     */
    public function addDiscountItem(int $discountId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/add_discount_item', [], [
            'discount_id' => $discountId,
            'item_list' => $itemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Apaga uma promocao (so upcoming) — /api/v2/discount/delete_discount.
     *
     * @return array<string,mixed>
     */
    public function deleteDiscount(int $discountId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/delete_discount', [], [
            'discount_id' => $discountId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove um item (ou uma variacao dele) da promocao —
     * /api/v2/discount/delete_discount_item. `model_id` so quando o item tem
     * variacao.
     *
     * @return array<string,mixed>
     */
    public function deleteDiscountItem(int $discountId, int $itemId, ?int $modelId = null): array
    {
        $body = ['discount_id' => $discountId, 'item_id' => $itemId];
        if ($modelId !== null) {
            $body['model_id'] = $modelId;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/delete_discount_item', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza nome/periodo de uma promocao — /api/v2/discount/update_discount.
     * So envia os campos informados. O novo start_time precisa ser posterior ao
     * original; end_time >= start_time + 1h.
     *
     * @return array<string,mixed>
     */
    public function updateDiscount(
        int $discountId,
        ?string $discountName = null,
        ?int $startTime = null,
        ?int $endTime = null,
    ): array {
        $body = array_filter([
            'discount_id' => $discountId,
            'discount_name' => $discountName,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/update_discount', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza preco/limite dos itens da promocao —
     * /api/v2/discount/update_discount_item. Maximo 50 itens por chamada;
     * cada item: item_id, item_promotion_price, purchase_limit,
     * model_list[{model_id, model_promotion_price}]. O estoque reservado NAO
     * e atualizavel aqui (apagar e re-adicionar o item).
     *
     * @param  list<array<string,mixed>>  $itemList
     * @return array<string,mixed>
     */
    public function updateDiscountItem(int $discountId, array $itemList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/update_discount_item', [], [
            'discount_id' => $discountId,
            'item_list' => $itemList,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra uma promocao em andamento (ongoing) — /api/v2/discount/end_discount.
     *
     * @return array<string,mixed>
     */
    public function endDiscount(int $discountId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/end_discount', [], [
            'discount_id' => $discountId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Descontos SIP (Shopee International Platform) das lojas afiliadas —
     * /api/v2/discount/get_sip_discounts. Chamar com o shop_id da loja
     * PRIMARIA; devolve so regioes com desconto upcoming/ongoing. `region`
     * filtra uma regiao especifica.
     *
     * @return array<string,mixed>
     */
    public function getSipDiscounts(?string $region = null): array
    {
        $query = $region !== null ? ['region' => $region] : [];

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/discount/get_sip_discounts', $query);

        return $response['response'] ?? [];
    }

    /**
     * Define/atualiza o desconto geral de uma regiao SIP afiliada —
     * /api/v2/discount/set_sip_discount (shop_id da loja primaria;
     * `sip_discount_rate` em percentual inteiro, vale para todos os itens).
     *
     * @return array<string,mixed>
     */
    public function setSipDiscount(string $region, int $sipDiscountRate): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/set_sip_discount', [], [
            'region' => $region,
            'sip_discount_rate' => $sipDiscountRate,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Remove o desconto SIP de uma regiao afiliada —
     * /api/v2/discount/delete_sip_discount (shop_id da loja primaria).
     *
     * @return array<string,mixed>
     */
    public function deleteSipDiscount(string $region): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/discount/delete_sip_discount', [], [
            'region' => $region,
        ]);

        return $response['response'] ?? [];
    }

    // ------------------------------------------------------------------
    // Voucher — detalhe + escrita (/api/v2/voucher/*). Devolvem `response` cru.
    // ------------------------------------------------------------------

    /**
     * Detalhe de um cupom — /api/v2/voucher/get_voucher (nome, codigo,
     * periodo, tipo, reward_type, usage_quantity, current_usage, min_basket_price,
     * discount_amount/percentage/max_price, item_id_list, display_channel_list).
     *
     * @return array<string,mixed>
     */
    public function getVoucher(int $voucherId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/voucher/get_voucher', [
            'voucher_id' => $voucherId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Cria um cupom — /api/v2/voucher/add_voucher.
     * Obrigatorios: voucher_name, voucher_code, start_time, end_time,
     * voucher_type (1 loja, 2 produto), reward_type (1 valor fixo, 2 percentual,
     * 3 coin cashback), usage_quantity, min_basket_price. Conforme o reward_type:
     * discount_amount (tipo 1) ou percentage + max_price (tipos 2/3).
     * Cupom de produto exige item_id_list. Opcionais: display_channel_list,
     * display_start_time. Devolve `voucher_id`.
     *
     * @param  array<string,mixed>  $payload
     * @return array<string,mixed>
     */
    public function addVoucher(array $payload): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/voucher/add_voucher', [], $payload);

        return $response['response'] ?? [];
    }

    /**
     * Atualiza um cupom — /api/v2/voucher/update_voucher. So envia
     * `voucher_id` + os campos informados em `$fields` (voucher_name,
     * start_time — so se ainda nao comecou —, end_time, usage_quantity,
     * min_basket_price, discount_amount, percentage, max_price,
     * display_channel_list, item_id_list, display_start_time).
     *
     * @param  array<string,mixed>  $fields
     * @return array<string,mixed>
     */
    public function updateVoucher(int $voucherId, array $fields): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/voucher/update_voucher', [], array_merge(
            ['voucher_id' => $voucherId],
            $fields,
        ));

        return $response['response'] ?? [];
    }

    /**
     * Apaga um cupom (so upcoming) — /api/v2/voucher/delete_voucher.
     *
     * @return array<string,mixed>
     */
    public function deleteVoucher(int $voucherId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/voucher/delete_voucher', [], [
            'voucher_id' => $voucherId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Encerra um cupom em andamento — /api/v2/voucher/end_voucher.
     *
     * @return array<string,mixed>
     */
    public function endVoucher(int $voucherId): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/voucher/end_voucher', [], [
            'voucher_id' => $voucherId,
        ]);

        return $response['response'] ?? [];
    }
}
