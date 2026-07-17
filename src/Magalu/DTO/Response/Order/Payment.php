<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pagamento (`payments[]`). `amount` é INT normalizado (÷ `normalizer`) — NÃO
 * é objeto Money aqui, os campos vêm soltos. `extras` é lista de shape livre.
 *
 * @property array<int, mixed>|null $extras
 */
final class Payment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $amount = null,
        public readonly ?int $normalizer = null,
        public readonly ?string $currency = null,
        public readonly ?string $method = null,
        public readonly ?string $methodBrand = null,
        public readonly ?string $brand = null,
        public readonly ?string $type = null,
        public readonly ?int $installments = null,
        public readonly ?string $description = null,
        public readonly ?string $authorizationCode = null,
        public readonly ?string $integrationType = null,
        public readonly ?Gateway $gateway = null,
        public readonly ?Acquirer $acquirer = null,
        public readonly ?array $extras = null,
    ) {}

    /** `amount` em reais (÷ normalizer). */
    public function value(): ?float
    {
        return $this->amount === null ? null : $this->amount / ($this->normalizer ?: 100);
    }
}
