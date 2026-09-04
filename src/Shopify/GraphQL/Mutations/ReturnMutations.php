<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio Return.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class ReturnMutations extends BaseOperations
{
    /**
     * Removes return and/or exchange lines from a return.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: returnId: ID!, returnLineItems: [ReturnLineItemRemoveFromReturnInput!], exchangeLineItems: [ExchangeLineItemRemoveFromReturnInput!]
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function removeFromReturn(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'removeFromReturn', $args, ['returnId' => 'ID!', 'returnLineItems' => '[ReturnLineItemRemoveFromReturnInput!]', 'exchangeLineItems' => '[ExchangeLineItemRemoveFromReturnInput!]'], $selection);
    }

    /**
     * Approves a customer's return request. If this mutation is successful, then the `Return.status` field of the approved return is set to `OPEN`.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ReturnApproveRequestInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnApproveRequest(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnApproveRequest', $args, ['input' => 'ReturnApproveRequestInput!'], $selection);
    }

    /**
     * Cancels a return and restores the items back to being fulfilled. Canceling a return is only available before any work has been done on the return (such as an inspection or refund).
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnCancel(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnCancel', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Indicates a return is complete, either when a refund has been made and items restocked, or simply when it has been marked as returned in the system.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnClose(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnClose', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a return from an existing order that has at least one fulfilled [line item](https://shopify.dev/docs/api/admin-graphql/latest/objects/LineItem) that hasn't yet been refunded. If you create a r
     *
     * @param array<string,mixed> $args Variaveis GraphQL: returnInput: ReturnInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnCreate(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnCreate', $args, ['returnInput' => 'ReturnInput!'], $selection);
    }

    /**
     * Declines a return on an order. When a return is declined, each `ReturnLineItem.fulfillmentLineItem` can be associated to a new return. Use the `ReturnCreate` or `ReturnRequest` mutation to initiate a
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ReturnDeclineRequestInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnDeclineRequest(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnDeclineRequest', $args, ['input' => 'ReturnDeclineRequestInput!'], $selection);
    }

    /**
     * Removes return lines from a return.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: returnId: ID!, returnLineItems: [ReturnLineItemRemoveFromReturnInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnLineItemRemoveFromReturn(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnLineItemRemoveFromReturn', $args, ['returnId' => 'ID!', 'returnLineItems' => '[ReturnLineItemRemoveFromReturnInput!]!'], $selection);
    }

    /**
     * Processes a return by confirming which items customers return and exchange, handling their disposition, and optionally issuing refunds. This mutation confirms the quantities for [`ReturnLineItem`](htt
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ReturnProcessInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnProcess(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnProcess', $args, ['input' => 'ReturnProcessInput!'], $selection);
    }

    /**
     * Creates a refund for items being returned when the return status is `OPEN` or `CLOSED`. This mutation processes the financial aspects of a return by refunding line items, shipping costs, and duties ba
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: returnRefundInput: ReturnRefundInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnRefund(array $args = [], string $selection = 'refund { createdAt id legacyResourceId note processedAt updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnRefund', $args, ['returnRefundInput' => 'ReturnRefundInput!'], $selection);
    }

    /**
     * Reopens a closed return.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: id: ID!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnReopen(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnReopen', $args, ['id' => 'ID!'], $selection);
    }

    /**
     * Creates a return request that requires merchant approval before processing. The return has its status set to `REQUESTED` and the merchant must approve or decline it. Use this mutation when customers
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: ReturnRequestInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function returnRequest(array $args = [], string $selection = 'return { closedAt createdAt id name requestApprovedAt status totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'returnRequest', $args, ['input' => 'ReturnRequestInput!'], $selection);
    }

    /**
     * Creates a new reverse delivery with associated external shipping information.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: reverseFulfillmentOrderId: ID!, reverseDeliveryLineItems: [ReverseDeliveryLineItemInput!]!, trackingInput: ReverseDeliveryTrackingInput, labelInput: ReverseDeliveryLabelInput, notifyCustomer: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function reverseDeliveryCreateWithShipping(array $args = [], string $selection = 'reverseDelivery { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'reverseDeliveryCreateWithShipping', $args, ['reverseFulfillmentOrderId' => 'ID!', 'reverseDeliveryLineItems' => '[ReverseDeliveryLineItemInput!]!', 'trackingInput' => 'ReverseDeliveryTrackingInput', 'labelInput' => 'ReverseDeliveryLabelInput', 'notifyCustomer' => 'Boolean'], $selection);
    }

    /**
     * Updates a reverse delivery with associated external shipping information.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: reverseDeliveryId: ID!, trackingInput: ReverseDeliveryTrackingInput, labelInput: ReverseDeliveryLabelInput, notifyCustomer: Boolean
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function reverseDeliveryShippingUpdate(array $args = [], string $selection = 'reverseDelivery { id } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'reverseDeliveryShippingUpdate', $args, ['reverseDeliveryId' => 'ID!', 'trackingInput' => 'ReverseDeliveryTrackingInput', 'labelInput' => 'ReverseDeliveryLabelInput', 'notifyCustomer' => 'Boolean'], $selection);
    }

    /**
     * Disposes reverse fulfillment order line items.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: dispositionInputs: [ReverseFulfillmentOrderDisposeInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function reverseFulfillmentOrderDispose(array $args = [], string $selection = 'reverseFulfillmentOrderLineItems { id totalQuantity } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'reverseFulfillmentOrderDispose', $args, ['dispositionInputs' => '[ReverseFulfillmentOrderDisposeInput!]!'], $selection);
    }
}
