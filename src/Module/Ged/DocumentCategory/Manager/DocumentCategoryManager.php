<?php

declare(strict_types=1);

namespace App\Module\Ged\DocumentCategory\Manager;

use App\Module\Ged\DocumentCategory\Dto\DocumentCategoryInput;
use App\Module\Ged\DocumentCategory\Entity\DocumentCategory;
use Aurora\Module\Ged\DocumentCategory\Dto\DocumentCategoryInputInterface;
use Aurora\Module\Ged\DocumentCategory\Entity\DocumentCategoryInterface;
use Aurora\Module\Ged\DocumentCategory\Manager\DocumentCategoryManager as AuroraDocumentCategoryManager;
use Aurora\Module\Ged\DocumentCategory\Manager\DocumentCategoryManagerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Layer 3 of the client extension - reference example.
 *
 * Three overrides, and each one exists for a reason worth knowing:
 *
 * - `createDocumentCategory()` must return the *client* entity. Aurora's
 *   version news up its own class, which has no `color` column, so skipping
 *   this hook loses the field with no error anywhere.
 * - `applyInput()` must call `parent::` FIRST. The parent sets name,
 *   description and the unique slug; replacing it instead of extending it
 *   leaves an entity with a colour and nothing else.
 * - `auditPayload()` spread-merges so the audit log records the colour
 *   alongside whatever Aurora already logs.
 *
 * The constructor is inherited untouched: Aurora declares its dependencies
 * `protected readonly` precisely so a client subclass can reuse them.
 */
#[AsAlias(DocumentCategoryManagerInterface::class)]
class DocumentCategoryManager extends AuroraDocumentCategoryManager
{
    #[Override]
    protected function createDocumentCategory(): DocumentCategoryInterface
    {
        return new DocumentCategory();
    }

    #[Override]
    protected function applyInput(DocumentCategoryInterface $category, DocumentCategoryInputInterface $input): void
    {
        parent::applyInput($category, $input);

        if ($category instanceof DocumentCategory && $input instanceof DocumentCategoryInput) {
            $category->setColor($input->getColor());
        }
    }

    /** @return array<string, mixed> */
    #[Override]
    protected function auditPayload(DocumentCategoryInterface $category): array
    {
        return [
            ...parent::auditPayload($category),
            'color' => $category instanceof DocumentCategory ? $category->getColor() : null,
        ];
    }
}
