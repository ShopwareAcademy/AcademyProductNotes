<?php declare(strict_types=1);

namespace AcademyProductNotes\Core\Content\Product;

use AcademyProductNotes\Core\Content\ProductNote\ProductNoteDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField('academyProductNotes', ProductNoteDefinition::class, 'product_id')
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    public function getEntityName(): string
    {
        return 'product';
    }
} 