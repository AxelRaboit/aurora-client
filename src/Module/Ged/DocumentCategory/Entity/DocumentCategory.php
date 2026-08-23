<?php

declare(strict_types=1);

namespace App\Module\Ged\DocumentCategory\Entity;

use App\Module\Ged\DocumentCategory\Manager\DocumentCategoryManager;
use Aurora\Module\Ged\DocumentCategory\Entity\AbstractDocumentCategory;
use Aurora\Module\Ged\DocumentCategory\Repository\DocumentCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Layer 1 of the client extension - reference example.
 *
 * Adds a `color` field to Aurora's document categories without touching
 * vendor/. Extending `AbstractDocumentCategory` rather than the concrete
 * `DocumentCategory` is what lets `resolve_target_entities` swap this class
 * in everywhere Aurora type-hints `DocumentCategoryInterface`.
 *
 * Table and sequence carry the `app_` prefix: `core_*` and `seq_core_*`
 * belong to Aurora, and SequencePrefixConflictListener throws at boot if a
 * client reuses them.
 *
 * @see DocumentCategoryManager
 */
#[ORM\Entity(repositoryClass: DocumentCategoryRepository::class)]
#[ORM\Table(name: 'app_document_categories')]
class DocumentCategory extends AbstractDocumentCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'seq_app_document_category_id', allocationSize: 1)]
    #[ORM\Column]
    protected ?int $id = null;

    /** Hex colour used to tint the category badge, e.g. `#6366f1`. */
    #[ORM\Column(length: 7, nullable: true)]
    protected ?string $color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
