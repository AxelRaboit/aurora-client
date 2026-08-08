<?php

declare(strict_types=1);

namespace App\Module\Ged\DocumentCategory\Dto;

use Aurora\Core\Support\Str;
use Aurora\Module\Ged\DocumentCategory\Dto\DocumentCategoryInputFactory as AuroraDocumentCategoryInputFactory;
use Aurora\Module\Ged\DocumentCategory\Dto\DocumentCategoryInputFactoryInterface;
use Aurora\Module\Ged\DocumentCategory\Dto\DocumentCategoryInputInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * Layer 2 of the client extension — reference example.
 *
 * `#[AsAlias]` is what makes controllers resolve this factory instead of
 * Aurora's: they type-hint `DocumentCategoryInputFactoryInterface`, and the
 * alias repoints that interface at this class. Without it the client DTO is
 * never built and `color` silently never arrives.
 *
 * Mind the import of that interface. `DocumentCategoryInputFactoryInterface::class`
 * without a `use` resolves against the *current* namespace, so the attribute
 * would alias `App\…\DocumentCategoryInputFactoryInterface` — a name nothing
 * implements. PHP accepts it, phpstan accepts it, and the override simply
 * never happens. `php bin/console debug:autowiring DocumentCategory` is what
 * shows it: the interface still points at the Aurora class.
 */
#[AsAlias(DocumentCategoryInputFactoryInterface::class)]
class DocumentCategoryInputFactory extends AuroraDocumentCategoryInputFactory
{
    /** @param array<string, mixed> $data */
    #[Override]
    public function fromArray(array $data): DocumentCategoryInputInterface
    {
        return new DocumentCategoryInput(
            name: Str::trimFromArray($data, 'name'),
            description: Str::trimOrNullFromArray($data, 'description'),
            color: Str::trimOrNullFromArray($data, 'color'),
        );
    }
}
