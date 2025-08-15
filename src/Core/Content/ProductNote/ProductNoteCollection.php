<?php declare(strict_types=1);

namespace AcademyProductNotes\Core\Content\ProductNote;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ProductNoteEntity>
 */
class ProductNoteCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductNoteEntity::class;
    }
} 