<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Enum;

/**
 * Grupos de billing ML — define qual relatorio o endpoint /billing/*
 * retorna ou qual report assincrono gerar.
 *
 * Doc: https://developers.mercadolivre.com.br/pt_br/boas-praticas-para-o-consumo-das-apis-de-relatorios-de-faturamento
 *
 * Reaproveitado em todos os endpoints que aceitam `group` (summary,
 * details, payment_details, reports POST).
 */
enum BillingGroup: string
{
    /** Mercado Livre — comissoes, frete, ADS, taxa fixa. Caso principal. */
    case ML = 'ML';

    /** Mercado Pago — taxas de pagamento (parcelamento, antecipacao). */
    case MP = 'MP';

    /** Mercado Envios Flex — fretes pra entregadores parceiros. So reports. */
    case FLEX = 'FLEX';

    /** Fulfillment ML — armazenagem + pick&pack. So reports. */
    case FULL = 'FULL';

    /** Insurtech — seguros do produto. So reports. */
    case INSURTECH = 'INSURTECH';

    /** Pagamentos abonados — NFs liquidadas. So reports. */
    case PAYMENT = 'PAYMENT';

    /**
     * True se o group eh compativel com os endpoints /summary e /details
     * sincronos. Demais valores so funcionam via reports assincronos
     * (POST /reports + status + download).
     */
    public function supportsSyncEndpoints(): bool
    {
        return match ($this) {
            self::ML, self::MP => true,
            default => false,
        };
    }
}
