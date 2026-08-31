<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `/promotion/202607/activities/{activity_id}/republish`.
 *
 * O `activityId` devolvido e' o da activity NOVA (uma copia reagendada), nao o
 * que foi passado no path: republicar nao "revive" a campanha expirada, cria
 * outra. Guardar o id de retorno — e' por ele que se acompanha/desativa a
 * campanha republicada.
 */
final class RepublishActivityResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $activityId = null,
    ) {}
}
