<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Métricas de atendimento da LOJA no período.
 *
 * Percentuais e valores vêm como STRING ("93.4", "36500") — o TikTok não manda
 * número aqui. E são PERCENTUAIS já em base 100, não fração.
 */
final class CustomerServicePerformance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $supportSessionCount = null,
        /** % de sessões com 1a resposta em até 24h. "93.4" = 93,4%. */
        public readonly ?string $responsePercentage = null,
        /** % de sessões avaliadas com 4 ou 5 estrelas. */
        public readonly ?string $satisfactionPercentage = null,
        /** Tempo médio de 1a resposta em MINUTOS. */
        public readonly ?string $responseTimeMins = null,
        public readonly ?string $conversionRate = null,
        /** Receita de pedidos feitos até 7 dias depois da resposta no chat. */
        public readonly ?string $csGuidedGmv = null,
        public readonly ?string $currency = null,
    ) {}
}
