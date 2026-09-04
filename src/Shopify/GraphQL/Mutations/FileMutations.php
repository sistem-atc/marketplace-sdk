<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Mutations;

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/**
 * Mutations da Shopify Admin API GraphQL (schema 2026-07) — dominio File.
 *
 * ARQUIVO GERADO a partir do schema; nao editar a mao. Cada metodo monta
 * `mutation <name>($var: Tipo, ...) { <name>(var: $var) { <selection> } }`
 * declarando so as variaveis presentes em $args. Mutations devolvem o payload
 * com `userErrors` dentro (nao lancam excecao por erro de negocio).
 */
final class FileMutations extends BaseOperations
{
    /**
     * Acknowledges file update failure by resetting FAILED status to READY and clearing any media errors.
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fileIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fileAcknowledgeUpdateFailed(array $args = [], string $selection = 'files { alt createdAt fileStatus id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fileAcknowledgeUpdateFailed', $args, ['fileIds' => '[ID!]!'], $selection);
    }

    /**
     * Creates file assets for a store from external URLs or files that were previously uploaded using the [`stagedUploadsCreate`](https://shopify.dev/docs/api/admin-graphql/latest/mutations/stageduploadscre
     *
     * @param array<string,mixed> $args Variaveis GraphQL: files: [FileCreateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fileCreate(array $args = [], string $selection = 'files { alt createdAt fileStatus id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fileCreate', $args, ['files' => '[FileCreateInput!]!'], $selection);
    }

    /**
     * Deletes file assets that were previously uploaded to your store. Use the `fileDelete` mutation to permanently remove media and file assets from your store when they are no longer needed. This mutatio
     *
     * @param array<string,mixed> $args Variaveis GraphQL: fileIds: [ID!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fileDelete(array $args = [], string $selection = 'deletedFileIds userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fileDelete', $args, ['fileIds' => '[ID!]!'], $selection);
    }

    /**
     * Updates properties, content, and metadata associated with an existing file asset that has already been uploaded to Shopify. Use the `fileUpdate` mutation to modify various aspects of files already st
     *
     * @param array<string,mixed> $args Variaveis GraphQL: files: [FileUpdateInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function fileUpdate(array $args = [], string $selection = 'files { alt createdAt fileStatus id updatedAt } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'fileUpdate', $args, ['files' => '[FileUpdateInput!]!'], $selection);
    }

    /**
     * Generates the URL and signed paramaters needed to upload an asset to Shopify.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: StagedUploadTargetGenerateInput!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function stagedUploadTargetGenerate(array $args = [], string $selection = 'parameters { name value } url userErrors { field message }'): array
    {
        return $this->execute('mutation', 'stagedUploadTargetGenerate', $args, ['input' => 'StagedUploadTargetGenerateInput!'], $selection);
    }

    /**
     * Uploads multiple images.
     *
     * @deprecated Marcada como deprecated no schema 2026-07.
     * @param array<string,mixed> $args Variaveis GraphQL: input: [StageImageInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function stagedUploadTargetsGenerate(array $args = [], string $selection = 'urls { url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'stagedUploadTargetsGenerate', $args, ['input' => '[StageImageInput!]!'], $selection);
    }

    /**
     * Creates staged upload targets for file uploads such as images, videos, and 3D models. Use the `stagedUploadsCreate` mutation instead of direct file creation mutations when: - **Uploading large files
     *
     * @param array<string,mixed> $args Variaveis GraphQL: input: [StagedUploadInput!]!
     * @param string $selection Selection set (default: campos escalares do retorno)
     * @return array<string,mixed>
     */
    public function stagedUploadsCreate(array $args = [], string $selection = 'stagedTargets { resourceUrl url } userErrors { field message }'): array
    {
        return $this->execute('mutation', 'stagedUploadsCreate', $args, ['input' => '[StagedUploadInput!]!'], $selection);
    }
}
