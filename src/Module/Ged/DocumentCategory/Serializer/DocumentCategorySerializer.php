<?php

declare(strict_types=1);

namespace App\Module\Ged\DocumentCategory\Serializer;

use App\Module\Ged\DocumentCategory\Entity\DocumentCategory;
use Aurora\Module\Ged\DocumentCategory\Entity\DocumentCategoryInterface;
use Aurora\Module\Ged\DocumentCategory\Serializer\DocumentCategorySerializer as AuroraDocumentCategorySerializer;
use Aurora\Module\Ged\DocumentCategory\Serializer\DocumentCategorySerializerInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Layer 4 of the client extension — reference example.
 *
 * Spread-merges rather than rebuilding the payload: every field Aurora adds
 * upstream keeps flowing to the front without this file changing.
 *
 * The `instanceof` guard is not ceremony — the interface Aurora hands us
 * knows nothing about `getColor()`, and a project can still hold rows
 * created before the extension existed.
 */
#[AsAlias(DocumentCategorySerializerInterface::class)]
class DocumentCategorySerializer extends AuroraDocumentCategorySerializer
{
    /** @return array<string, mixed> */
    #[Override]
    public function serialize(DocumentCategoryInterface $category): array
    {
        return [
            ...parent::serialize($category),
            'color' => $category instanceof DocumentCategory ? $category->getColor() : null,
        ];
    }
}
