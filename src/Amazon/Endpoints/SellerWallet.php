<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Seller Wallet API 2024-03-01 — contas Seller Wallet (a "conta em moeda
 * estrangeira" da Amazon): saldos, transações, agendamentos de transferência.
 * Todo endpoint exige `marketplaceId` na query. Rate limits não publicados
 * no modelo.
 *
 * createTransaction, createTransferSchedule e updateTransferSchedule exigem
 * os headers `destAccountDigitalSignature` (assinatura dos dados da conta
 * destino) e `amountDigitalSignature` (assinatura do valor na moeda de
 * origem). Esses valores NÃO são gerados aqui: vêm do Seller Central /
 * assinatura do seller conforme o "Third-Party Provider Signature Guidance"
 * da Amazon, e entram como argumentos obrigatórios que viram headers.
 */
class SellerWallet
{
    private const BASE = '/finances/transfers/wallet/2024-03-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Contas Seller Wallet do seller (GET …/accounts). Retorna `accounts[]`.
     *
     * @return array<string, mixed>
     */
    public function listAccounts(string $marketplaceId): array
    {
        return $this->client->get(self::BASE.'/accounts', ['marketplaceId' => $marketplaceId]);
    }

    /**
     * Uma conta (GET …/accounts/{accountId}): titular, banco, moeda, país,
     * status. Retorna o objeto BankAccount no topo.
     *
     * @return array<string, mixed>
     */
    public function getAccount(string $accountId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/accounts/'.rawurlencode($accountId),
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Saldos da conta (GET …/accounts/{accountId}/balance). Retorna `balances[]`
     * (tipo + valor/moeda).
     *
     * @return array<string, mixed>
     */
    public function listAccountBalances(string $accountId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/accounts/'.rawurlencode($accountId).'/balance',
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Prévia de uma transferência entre moedas (GET …/transferPreview): taxa
     * de câmbio, fees e valor líquido. Todos os parâmetros obrigatórios.
     *
     * @return array<string, mixed>
     */
    public function getTransferPreview(
        string $marketplaceId,
        string $sourceCountryCode,
        string $sourceCurrencyCode,
        string $destinationCountryCode,
        string $destinationCurrencyCode,
        float $baseAmount,
    ): array {
        return $this->client->get(self::BASE.'/transferPreview', [
            'sourceCountryCode' => $sourceCountryCode,
            'sourceCurrencyCode' => $sourceCurrencyCode,
            'destinationCountryCode' => $destinationCountryCode,
            'destinationCurrencyCode' => $destinationCurrencyCode,
            'baseAmount' => $baseAmount,
            'marketplaceId' => $marketplaceId,
        ]);
    }

    /**
     * Transações da conta (GET …/transactions?accountId=…). Paginado por
     * `nextPageToken`. Retorna `transactions[]` + `nextPageToken`.
     *
     * @return array<string, mixed>
     */
    public function listAccountTransactions(string $accountId, string $marketplaceId, ?string $nextPageToken = null): array
    {
        $query = ['accountId' => $accountId, 'marketplaceId' => $marketplaceId];
        if ($nextPageToken !== null) {
            $query['nextPageToken'] = $nextPageToken;
        }

        return $this->client->get(self::BASE.'/transactions', $query);
    }

    /**
     * Uma transação (GET …/transactions/{transactionId}): tipo, status,
     * datas, contas origem/destino, valores e câmbio.
     *
     * @return array<string, mixed>
     */
    public function getTransaction(string $transactionId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/transactions/'.rawurlencode($transactionId),
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Agendamentos de transferência da conta (GET …/transferSchedules?accountId=…).
     * Paginado por `nextPageToken`. Retorna `transferSchedules[]`.
     *
     * @return array<string, mixed>
     */
    public function listTransferSchedules(string $accountId, string $marketplaceId, ?string $nextPageToken = null): array
    {
        $query = ['accountId' => $accountId, 'marketplaceId' => $marketplaceId];
        if ($nextPageToken !== null) {
            $query['nextPageToken'] = $nextPageToken;
        }

        return $this->client->get(self::BASE.'/transferSchedules', $query);
    }

    /**
     * Um agendamento (GET …/transferSchedules/{transferScheduleId}).
     *
     * @return array<string, mixed>
     */
    public function getTransferSchedule(string $transferScheduleId, string $marketplaceId): array
    {
        return $this->client->get(
            self::BASE.'/transferSchedules/'.rawurlencode($transferScheduleId),
            ['marketplaceId' => $marketplaceId],
        );
    }

    /**
     * Cancela um agendamento (DELETE …/transferSchedules/{transferScheduleId}).
     * Retorna {code, message, details}.
     *
     * @return array<string, mixed>
     */
    public function deleteScheduleTransaction(string $transferScheduleId, string $marketplaceId): array
    {
        return $this->client->delete(
            self::BASE.'/transferSchedules/'.rawurlencode($transferScheduleId)
            .'?'.http_build_query(['marketplaceId' => $marketplaceId]),
        );
    }

    /**
     * Cria uma transação (transferência) a partir de uma conta Seller Wallet
     * (POST …/transactions?marketplaceId=…). Retorna o objeto Transaction.
     *
     * @param  array<string, mixed>  $body  TransactionInitiationRequest (sourceAccountId, sourceTransactionAmount, destinationAccountId, ...)
     * @param  string  $destAccountDigitalSignature  Header `destAccountDigitalSignature` (assinatura da conta destino, obtida no Seller Central)
     * @param  string  $amountDigitalSignature  Header `amountDigitalSignature` (assinatura do valor de origem)
     * @return array<string, mixed>
     */
    public function createTransaction(
        string $marketplaceId,
        array $body,
        string $destAccountDigitalSignature,
        string $amountDigitalSignature,
    ): array {
        return $this->client->post(
            self::BASE.'/transactions?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
            $this->signatureHeaders($destAccountDigitalSignature, $amountDigitalSignature),
        );
    }

    /**
     * Cria um agendamento de transferência
     * (POST …/transferSchedules?marketplaceId=…). Retorna o TransferSchedule.
     *
     * @param  array<string, mixed>  $body  TransferScheduleRequest
     * @param  string  $destAccountDigitalSignature  Header `destAccountDigitalSignature` (Seller Central)
     * @param  string  $amountDigitalSignature  Header `amountDigitalSignature` (Seller Central)
     * @return array<string, mixed>
     */
    public function createTransferSchedule(
        string $marketplaceId,
        array $body,
        string $destAccountDigitalSignature,
        string $amountDigitalSignature,
    ): array {
        return $this->client->post(
            self::BASE.'/transferSchedules?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
            $this->signatureHeaders($destAccountDigitalSignature, $amountDigitalSignature),
        );
    }

    /**
     * Atualiza um agendamento de transferência
     * (PUT …/transferSchedules?marketplaceId=…). O id do agendamento vai no
     * body (TransferSchedule.transferScheduleId). Retorna o TransferSchedule.
     *
     * @param  array<string, mixed>  $body  TransferSchedule
     * @param  string  $destAccountDigitalSignature  Header `destAccountDigitalSignature` (Seller Central)
     * @param  string  $amountDigitalSignature  Header `amountDigitalSignature` (Seller Central)
     * @return array<string, mixed>
     */
    public function updateTransferSchedule(
        string $marketplaceId,
        array $body,
        string $destAccountDigitalSignature,
        string $amountDigitalSignature,
    ): array {
        return $this->client->put(
            self::BASE.'/transferSchedules?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
            $this->signatureHeaders($destAccountDigitalSignature, $amountDigitalSignature),
        );
    }

    /** @return array<string, string> */
    private function signatureHeaders(string $destAccountDigitalSignature, string $amountDigitalSignature): array
    {
        return [
            'destAccountDigitalSignature' => $destAccountDigitalSignature,
            'amountDigitalSignature' => $amountDigitalSignature,
        ];
    }
}
