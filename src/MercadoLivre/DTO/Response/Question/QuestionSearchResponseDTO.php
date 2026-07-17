<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Question;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * GET /questions/search — wrapper paginado (`questions[]`, não `results[]`).
 * Também é o retorno de unansweredByItem().
 *
 * @property list<QuestionResponseDTO> $questions
 */
final class QuestionSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<QuestionResponseDTO> $questions */
    public function __construct(
        #[ArrayOf(QuestionResponseDTO::class)]
        public readonly array $questions = [],
        public readonly ?int $total = null,
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly mixed $filters = null,
        public readonly mixed $availableFilters = null,
        public readonly mixed $availableSorts = null,
        public readonly mixed $sort = null,
    ) {}
}
