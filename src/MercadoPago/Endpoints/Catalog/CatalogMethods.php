<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Catalog;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog\IdentificationTypeResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Catalog\PaymentMethodResponseDTO;

/**
 * Catalogos de referencia da conta: meios de pagamento e tipos de
 * documento. Espelha `PaymentMethodClient` e `IdentificationTypeClient`.
 *
 * Doc:
 *   https://www.mercadopago.com.br/developers/pt/reference/payment_methods/_payment_methods/get
 *   https://www.mercadopago.com.br/developers/pt/reference/identification_types/_identification_types/get
 */
class CatalogMethods extends BaseMethods
{
    /**
     * Meios de pagamento habilitados na conta (id, name, payment_type_id,
     * status, min/max_allowed_amount, settings de seguranca).
     *
     * @return list<PaymentMethodResponseDTO>
     */
    public function paymentMethods(): array
    {
        return $this->hydrateList($this->makeRequest(HttpMethod::GET, '/v1/payment_methods'), PaymentMethodResponseDTO::class);
    }

    /**
     * Tipos de documento do site (CPF/CNPJ no MLB) com min/max_length.
     *
     * @return list<IdentificationTypeResponseDTO>
     */
    public function identificationTypes(): array
    {
        return $this->hydrateList($this->makeRequest(HttpMethod::GET, '/v1/identification_types'), IdentificationTypeResponseDTO::class);
    }
}
