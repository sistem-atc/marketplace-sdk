<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Payment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\EscrowDetailResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\WalletTransaction;

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

    /**
     * Transações da CARTEIRA num período — o nível SETTLEMENT/payout: dinheiro
     * que de fato entrou/saiu da conta Shopee do vendedor (o repasse real), vs o
     * escrow por-pedido. Equivalente ao financialEventGroups da Amazon.
     *
     * Params: create_time_from / create_time_to (unix), page_no (1-based),
     * page_size (≤100), wallet_type, transaction_type, money_flow.
     *
     * @param  array<string, mixed>  $params
     * @return array{transactions: list<WalletTransaction>, more: bool}
     */
    public function getWalletTransactionList(array $params): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_wallet_transaction_list', $params);
        $resp = $response['response'] ?? [];

        return [
            'transactions' => array_map(
                static fn (array $t): WalletTransaction => WalletTransaction::fromArray($t),
                $resp['transaction_list'] ?? [],
            ),
            'more' => (bool) ($resp['more'] ?? false),
        ];
    }
}
