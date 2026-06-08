<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Enum;

/**
 * Status de pagamento Mercado Pago.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/docs/your-integrations/api-reference/payments
 */
enum PaymentStatus: string
{
    /** Pagamento criado mas ainda nao processado. */
    case PENDING = 'pending';

    /** Aprovado pelo MP — dinheiro liberado pra conta MP do seller. */
    case APPROVED = 'approved';

    /** Em analise antifraude. */
    case IN_PROCESS = 'in_process';

    /** Rejeitado (cartao invalido, fundos insuficientes, antifraude). */
    case REJECTED = 'rejected';

    /** Cancelado pelo seller/comprador antes de aprovar. */
    case CANCELLED = 'cancelled';

    /** Estornado apos aprovacao (total). */
    case REFUNDED = 'refunded';

    /** Chargeback iniciado pelo banco do comprador. */
    case CHARGED_BACK = 'charged_back';

    /** Pagamento aprovado mas devolveu parcialmente. */
    case PARTIALLY_REFUNDED = 'partially_refunded';

    /**
     * True quando o pagamento gera receita realizada (entrou na conta MP).
     * Eh o status que importa pra "contas a receber" — APPROVED ou
     * PARTIALLY_REFUNDED (esse ultimo ainda entrou, so devolveu parte).
     */
    public function isRevenueRealized(): bool
    {
        return match ($this) {
            self::APPROVED, self::PARTIALLY_REFUNDED => true,
            default => false,
        };
    }
}
