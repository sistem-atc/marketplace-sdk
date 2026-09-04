<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Return;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;

class ReturnMethods extends BaseMethods
{
    public function getReturnList(int $pageNo = 0, int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_return_list', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);
    }

    public function getReturnDetail(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_return_detail', [
            'return_sn' => $returnSn,
        ]);
    }

    public function confirmReturn(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/confirm', [], [
            'return_sn' => $returnSn,
        ]);
    }

    // -----------------------------------------------------------------------
    // Disputa, contraproposta e provas (2026-09)
    // -----------------------------------------------------------------------

    /** Solucoes que o vendedor pode oferecer nesta devolucao (response.available_solutions[]). */
    public function getAvailableSolutions(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_available_solutions', ['return_sn' => $returnSn]);
    }

    /**
     * Contraproposta ao comprador. proposed_solution conforme ReturnSolution
     * (RETURN_REFUND, REFUND, ...); amount so em reembolso parcial.
     */
    public function offer(string $returnSn, string $proposedSolution, ?float $proposedAdjustedRefundAmount = null): array
    {
        $body = ['return_sn' => $returnSn, 'proposed_solution' => $proposedSolution];
        if ($proposedAdjustedRefundAmount !== null) $body['proposed_adjusted_refund_amount'] = $proposedAdjustedRefundAmount;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/offer', [], $body);
    }

    /** Aceita a proposta do comprador. */
    public function acceptOffer(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/accept_offer', [], ['return_sn' => $returnSn]);
    }

    /** Motivos de disputa validos pra esta devolucao (dispute_reason_id + exigencia de imagem). */
    public function getReturnDisputeReason(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_return_dispute_reason', ['return_sn' => $returnSn]);
    }

    /**
     * Abre disputa. image_list e obrigatoria em quase todos os motivos
     * (exceto "nao recebi o produto"): [{module_index, requirement,
     * image_url[]}] com URLs vindas de convertImage().
     *
     * @param list<array{module_index:int,requirement?:string,image_url:list<string>}>|null $imageList
     */
    public function dispute(string $returnSn, string $email, int $disputeReasonId, ?array $imageList = null, ?string $disputeTextReason = null): array
    {
        $body = ['return_sn' => $returnSn, 'email' => $email, 'dispute_reason_id' => $disputeReasonId];
        if ($imageList !== null) $body['image_list'] = array_values($imageList);
        if ($disputeTextReason !== null) $body['dispute_text_reason'] = $disputeTextReason;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/dispute', [], $body);
    }

    /** Cancela disputa de COMPENSACAO (disputa normal nao cancela). email so pra registro. */
    public function cancelDispute(string $returnSn, string $email): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/cancel_dispute', [], ['return_sn' => $returnSn, 'email' => $email]);
    }

    /** Provas ja enviadas na devolucao (fotos + descricao). */
    public function queryProof(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/query_proof', ['return_sn' => $returnSn]);
    }

    /**
     * Sobe UMA imagem (jpg/jpeg/png, ate 10MB, multipart) e devolve
     * response.url + response.thumbnail pra usar em uploadProof()/dispute().
     */
    public function convertImage(string $returnSn, string $contents, string $filename): array
    {
        $apiPath = $this->normalizeApiPath('/api/v2/returns/convert_image');
        $authQuery = $this->buildAuthQuery($apiPath, false);

        $client = $this->multipartClient()->attach('upload_image', $contents, $filename);
        $response = $client->post($apiPath.'?'.http_build_query($authQuery), ['return_sn' => $returnSn]);

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) throw new ShopeeRequestException($response);

        return $data;
    }

    /**
     * Anexa provas (fotos ja convertidas + descricao) a devolucao.
     *
     * @param list<array{url:string,thumbnail?:string}>|null $photos
     */
    public function uploadProof(string $returnSn, ?array $photos = null, ?string $description = null): array
    {
        $body = ['return_sn' => $returnSn];
        if ($photos !== null) $body['photo'] = array_values($photos);
        if ($description !== null) $body['description'] = $description;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/upload_proof', [], $body);
    }

    /**
     * Transportadoras de logistica reversa NAO integrada aceitas nesta
     * devolucao, com flags is_tracking_number_required /
     * is_shipping_image_file_mandatory que governam o uploadShippingProof().
     */
    public function getShippingCarrier(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_shipping_carrier', ['return_sn' => $returnSn]);
    }

    /**
     * Comprova o envio reverso pelo vendedor (carrier nao integrada).
     * image_id_list (ate 3 {image_id}) vem do upload de imagem de
     * media_space; exigencias conforme getShippingCarrier().
     *
     * @param list<array{image_id:string}>|null $imageIdList
     */
    public function uploadShippingProof(
        string $returnSn,
        int $reverseLogisticsCarrierId,
        ?string $reverseLogisticsCarrierName = null,
        ?string $trackingNumber = null,
        ?array $imageIdList = null,
        ?string $remarks = null,
    ): array {
        $body = ['return_sn' => $returnSn, 'reverse_logistics_carrier_id' => $reverseLogisticsCarrierId];
        if ($reverseLogisticsCarrierName !== null) $body['reverse_logistics_carrier_name'] = $reverseLogisticsCarrierName;
        if ($trackingNumber !== null) $body['tracking_number'] = $trackingNumber;
        if ($imageIdList !== null) $body['image_id_list'] = array_values($imageIdList);
        if ($remarks !== null) $body['remarks'] = $remarks;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/upload_shipping_proof', [], $body);
    }

    /** Rastreio da logistica reversa (response.tracking_info[]). */
    public function getReverseTrackingInfo(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_reverse_tracking_info', ['return_sn' => $returnSn]);
    }

}
