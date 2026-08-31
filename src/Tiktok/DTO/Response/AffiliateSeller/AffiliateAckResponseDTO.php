<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` vazio: o endpoint confirma a acao sem devolver payload.
 *
 * Usado por editOpenCollaborationSettings, editOpenCollaborationSampleRule,
 * removeTargetCollaboration, removeCreatorFromOpenCollaboration,
 * reviewSampleApplication e updateConversationStatus. Existe pra manter o
 * retorno tipado (em vez de `array`) e pra abrir espaco caso o TikTok passe a
 * devolver campos.
 */
final class AffiliateAckResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(

    ) {}
}
