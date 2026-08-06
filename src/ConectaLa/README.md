# Conecta Lá — conector (marca **Shophub**)

> ⚠️ **Shophub == Conecta Lá.** No Bunker esta integração aparece com o nome
> **`shophub`** (marca do seller). "Conecta Lá" é a **plataforma/API** que o
> Shophub usa (domínio `conectala.com.br`); o conector ficou com o nome técnico
> da API (`ConectaLa`). Anotado aqui pra não depender de memória.

## Acesso

```php
use SistemAtc\Marketplaces\MarketPlaces;

MarketPlaces::ConectaLa()->infos($integration)->store();   // smoke test de credencial
MarketPlaces::ConectaLa()->orders($integration)->queue();  // fila de pedidos
```

## Autenticação

Por **HEADER** (não OAuth). As chaves ficam nas `settings` da Integration e viram
os headers `x-*`:

| setting | header |
|---|---|
| `api_key` (obrigatória) | `x-api-key` |
| `store_key` | `x-store-key` |
| `provider_key` | `x-provider-key` |
| `store_seller_key` | `x-store-seller-key` |
| `user_email` | `x-user-email` |
| `email` | `x-email` |

Base (homolog): `http://teste.conectala.com.br/app/Api/V1` (config
`marketplaces.conectala.api_base`).

## Sincronismo — FILA (polling), sem webhook

A API **não tem webhook**. O modelo é fila:

- **Pedidos**: `orders()->queue()` (novos) → processa → `orders()->removeFromQueue($id)`.
- **Produtos**: `products()->modifiedQueue()` (alterados) → reflete → `products()->removeFromQueue($sku)`.

No Bunker isso vira um **scheduler de polling** (não há assinatura de webhook).
A única exceção "push" é a **cotação de frete**: a plataforma chama a URL do
seller (`POST .../cotacao`) — endpoint que o **Bunker expõe**, não é método daqui.

## Domínios (`MarketPlaces::ConectaLa()->…`)

`orders()`, `products()`, `variations()`, `infos()`, `brands()`, `categories()`,
`catalogs()`, `collections()`, `financial()`, `tracking()`, `companies()`,
`stores()`, `users()`.

Cobrem os **89 endpoints** da doc (variantes de filtro viram params). Retornam
`array`; **DTOs tipados** serão montados a partir das **respostas reais de
homologação** (o exemplo do Postman não é confiável pra tipar).
