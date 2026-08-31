<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `mcf_order` do webhook type 58 — pedido multi-canal fulfillado pelo armazem
 * do TikTok (o TikTok expede um pedido que foi VENDIDO em outro canal).
 *
 * Divergencia doc x exemplo: `consign_orders` e' []object na tabela e objeto
 * unico no exemplo oficial. Fica cru aqui; use `typedConsignOrders()`.
 */
final class McfOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<mixed>|null $consignOrders */
    public function __construct(
        /** Id do pedido no SEU OMS (ex.: prefixo "shopify..."). */
        public readonly ?string $externalOrderId = null,
        public readonly ?string $mcfOrderId = null,
        public readonly ?array $consignOrders = null,
        /** int64 epoch em SEGUNDOS de criacao do MCF order. */
        public readonly ?int $createTime = null,
    ) {}

    /** @return list<McfConsignOrder> */
    public function typedConsignOrders(): array
    {
        return self::hydrateOneOrMany($this->consignOrders, McfConsignOrder::class);
    }

    /**
     * Normaliza "objeto unico OU lista de objetos" pra lista de DTOs.
     *
     * Existe porque o exemplo oficial do type 58 contradiz a propria tabela de
     * parametros em DOIS niveis (`consign_orders` e `goods`). Aceitar as duas
     * formas e' mais barato do que apostar em uma e perder o payload real.
     *
     * @param  array<mixed>|null  $raw
     * @param  class-string  $dto
     * @return list<mixed>
     */
    public static function hydrateOneOrMany(?array $raw, string $dto): array
    {
        if ($raw === null || $raw === []) {
            return [];
        }

        $lista = array_is_list($raw) ? $raw : [$raw];

        return array_values(array_map(
            fn (array $item) => $dto::fromArray($item),
            array_filter($lista, 'is_array'),
        ));
    }
}
