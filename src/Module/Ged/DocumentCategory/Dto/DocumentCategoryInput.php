<?php

declare(strict_types=1);

namespace App\Module\Ged\DocumentCategory\Dto;

use Aurora\Module\Ged\DocumentCategory\Dto\DocumentCategoryInput as AuroraDocumentCategoryInput;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Layer 2 of the client extension - reference example.
 *
 * Extends Aurora's input rather than replacing it, so the constraints
 * declared upstream on `name` keep applying. Note the class is not
 * `readonly class`: Aurora's isn't either, precisely so a client can add a
 * property here - a `readonly class` parent would forbid it.
 */
class DocumentCategoryInput extends AuroraDocumentCategoryInput
{
    public function __construct(
        string $name = '',
        ?string $description = null,
        #[Assert\Regex(
            pattern: '/^#[0-9a-fA-F]{6}$/',
            message: 'app.ged.categories.errors.color_invalid',
        )]
        public readonly ?string $color = null,
    ) {
        parent::__construct($name, $description);
    }

    public function getColor(): ?string
    {
        return $this->color;
    }
}
