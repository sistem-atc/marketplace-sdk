<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Payment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\EscrowDetailResponseDTO;

class PaymentMethods extends BaseMethods
{
    /**
     * Taxas + repasse do pedido.
     *
     * ->orderIncome->escrowAmount = liquido que a Shopee paga;
     * ->buyerPaymentInfo->buyerTotalAmount = o que o comprador pagou.
     */
    public function getEscrowDetail(string $orderSn): EscrowDetailResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_escrow_detail', ['order_sn' => $orderSn]);

        return EscrowDetailResponseDTO::fromArray($response['response'] ?? []);
    }
}
