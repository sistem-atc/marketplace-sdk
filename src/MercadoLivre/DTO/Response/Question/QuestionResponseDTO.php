<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Question;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ de GET /questions/{id} — pergunta de PRÉ-venda (Q&A público do anúncio).
 * `answer` fica null enquanto a loja não responde.
 *
 * @property array<int|string, mixed> $tags
 * @property array<int|string, mixed> $aiCategories
 */
final class QuestionResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $tags
     * @param  array<int|string, mixed>  $aiCategories
     */
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $text = null,
        public readonly ?string $status = null,
        public readonly ?string $itemId = null,
        public readonly ?int $sellerId = null,
        public readonly ?string $dateCreated = null,
        public readonly ?QuestionFrom $from = null,
        public readonly ?QuestionAnswer $answer = null,
        public readonly ?bool $deletedFromListing = null,
        public readonly mixed $hold = null,
        public readonly array $tags = [],
        public readonly array $aiCategories = [],
    ) {}
}
