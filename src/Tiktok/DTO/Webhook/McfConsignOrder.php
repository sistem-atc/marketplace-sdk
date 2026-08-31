<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Consignacao (expedicao) de um MCF order — webhook type 58.
 *
 * Divergencia doc x exemplo: a tabela declara `goods` como []object, o exemplo
 * oficial manda um OBJETO unico. Por isso `goods` fica cru aqui e a tipagem sai
 * por `typedGoods()`, que aceita as duas formas sem perder campo.
 *
 * O exemplo ainda traz `carrier` ("USPS"), campo que a tabela de parametros NAO
 * lista — mapeado assim mesmo (regra: zero campo descartado).
 */
final class McfConsignOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<mixed>|null $goods */
    public function __construct(
        /** Id unico do consign order (ex.: "OBF12345678"). */
        public readonly ?string $id = null,
        /** Nulo enquanto o transportador nao coletou. */
        public readonly ?string $trackingNumber = null,
        /**
         * SHIPPED | HANDOVER | CLOSED | LOST | DAMAGE | COMPLETED | ABNORMAL.
         * O exemplo oficial mostra "processing", em minuscula e fora da lista —
         * mais um motivo pra nao virar enum PHP.
         */
        public readonly ?string $status = null,
        /** Presente no exemplo oficial, ausente da tabela de parametros. */
        public readonly ?string $carrier = null,
        public readonly ?array $goods = null,
        /**
         * true = cancelado pela plataforma FBT; false = cancelado pelo seu OMS.
         * A doc declara STRING (sample `true`); se o TikTok mandar boolean, o
         * cast pra string vira "1" — compare com cuidado.
         */
        public readonly ?string $isPlatformClosed = null,
        /** Motivo do travamento quando o status e' ABNORMAL/LOST etc. */
        public readonly ?string $issue = null,
        public readonly ?McfShippingProvider $shippingProvider = null,
    ) {}

    /**
     * `goods` tipado, aceitando lista (doc) ou objeto unico (exemplo oficial).
     *
     * @return list<McfConsignGoods>
     */
    public function typedGoods(): array
    {
        return McfOrder::hydrateOneOrMany($this->goods, McfConsignGoods::class);
    }
}
