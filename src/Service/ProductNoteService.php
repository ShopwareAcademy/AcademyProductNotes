<?php declare(strict_types=1);

namespace AcademyProductNotes\Service\ProductNoteService;

use AcademyProductNotes\Core\Content\Product\ProductExtension;
use AcademyProductNotes\Core\Content\ProductNote\ProductNoteDefinition;
use AcademyProductNotes\Core\Content\ProductNote\ProductNoteEntity;
use DateTimeImmutable;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Throwable;

/**
 * This service class contains examples for working with "product notes" via the DAL.
 * It is meant for learning purposes only.
 *
 * In a real-world Shopware Plugin, these methods would usually be called from controllers or event subscribers or other entry points.
 */
class ProductNoteService
{
    public function __construct(
        private readonly EntityRepository $productNoteRepository,
        private readonly EntityRepository $productRepository,
        private readonly LoggerInterface  $logger,
    )
    {
    }

    public function getProductWithNotes(string $productId, Context $context): ?ProductEntity
    {
        $criteria = new Criteria([$productId]);

        // Load the academyProductNotes association/extension
        $criteria->addAssociation(ProductExtension::EXTENSION_NAME);

        /** @var ProductEntity|null $product */
        $product = $this->productRepository->search($criteria, $context)->first();

        return $product;
    }

    public function createProductNote(string $productId, string $note, Context $context): ?string
    {
        $data = [
            [
                'productId' => $productId,
                'productVersionId' => Defaults::LIVE_VERSION,
                'note' => $note
            ]
        ];

        $result = $this->productNoteRepository->upsert($data, $context);

        return $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds()[0] ?? null;
    }

    /**
     * Example only: Using try-catch for error handling
     * In real projects, error handling is usually done in controllers or other entry points.
     */
    public function updateProductNoteContent(string $id, string $note, Context $context): bool
    {
        try {
            $data = [
                'id' => $id,
                'note' => $note
            ];

            $result = $this->productNoteRepository->upsert([$data], $context);
            $resultIds = $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? [];

            // Check if the update was successful
            return in_array($id, $resultIds, true);
        } catch (Throwable $t) {
            // Log the error for debugging
            $this->logger->error(
                'Failed to update product note content',
                [
                    'productNoteId' => $id,
                    'exception' => $t,
                    'exceptionCode' => $t->getCode(),
                    'exceptionMessage' => $t->getMessage(),
                ]
            );
            return false;

            // In a real application, you might want to throw a custom exception instead of returning false
            // throw $t;
        }
    }

    public function updateNoteContent(string $id, string $note, Context $context): bool
    {
        $data = [
            'id' => $id,
            'note' => $note,
        ];

        $result = $this->productNoteRepository->update([$data], $context);
        return in_array($id, $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? []);
    }

    public function createMultipleProductNotes(array $notesData, Context $context): array
    {
        $data = [];

        foreach ($notesData as $noteData) {
            $data[] = [
                'productId' => $noteData['productId'],
                'productVersionId' => Defaults::LIVE_VERSION,
                'note' => $noteData['note'],
            ];
        }

        $result = $this->productNoteRepository->upsert($data, $context);

        return $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? [];
    }

    public function bulkUpdateProductNotes(array $updates, Context $context): array
    {
        $data = [];

        foreach ($updates as $update) {
            if (isset($update['id'])) {
                // This is an update operation
                $data[] = [
                    'id' => $update['id'],
                    'note' => $update['note'],
                    'updatedAt' => new DateTimeImmutable(),
                ];
            } else {
                // This is a create operation
                $data[] = [
                    'productId' => $update['productId'],
                    'productVersionId' => Defaults::LIVE_VERSION,
                    'note' => $update['note'],
                ];
            }
        }

        $result = $this->productNoteRepository->upsert($data, $context);
        $resultEntityIds = $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? [];

        return [
            'created' => array_filter($resultEntityIds, fn($id) => !in_array($id, array_column($updates, 'id'))),
            'updated' => array_filter($resultEntityIds, fn($id) => in_array($id, array_column($updates, 'id'))),
        ];
    }

    public function deleteProductNote(string $id, Context $context): bool
    {
        try {
            $result = $this->productNoteRepository->delete([['id' => $id]], $context);
            $resultIds = $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? [];

            // Check if the deletion was successful
            return in_array($id, $resultIds, true);
        } catch (Throwable $t) {
            // Handle deletion errors appropriately
            return false;
        }
    }

    public function deleteMultipleProductNotes(array $ids, Context $context): array
    {
        $data = array_map(fn($id) => ['id' => $id], $ids);

        $result = $this->productNoteRepository->delete($data, $context);

        return $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds() ?? [];
    }

    /**
     * Example only: Validation and error handling when writing data via the DAL
     *
     * This method intentionally combines input validation and try-catch handling
     * to demonstrate a common pattern when writing data.
     *
     * In real projects, service methods usually return an ID or void, and the controller formats the response.
     * Exceptions are typically handled at entry points (controllers or others)
     *
     */
    public function createProductNoteWithValidation(string $productId, string $note, Context $context): array
    {
        // Basic validation
        if (empty($note)) {
            $this->logger->error(
                'Note content cannot be empty',
                [
                    'productId' => $productId,
                    'note' => $note,
                ]
            );
            throw new InvalidArgumentException('Note content cannot be empty');
        }

        if (strlen($note) > 1000) {
            $this->logger->error(
                'Note content is too long (max 1000 characters)',
                [
                    'productId' => $productId,
                    'note' => $note,
                ]
            );
            throw new InvalidArgumentException('Note content is too long (max 1000 characters)');
        }

        try {
            $data = [
                'productId' => $productId,
                'productVersionId' => Defaults::LIVE_VERSION,
                'note' => trim($note),
            ];

            $result = $this->productNoteRepository->upsert([$data], $context);

            return [
                'success' => true,
                'id' => $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds()[0],
                'message' => 'Product note created successfully'
            ];
        } catch (Throwable $t) {
            $this->logger->error(
                'Failed to create product note',
                [
                    'productId' => $productId,
                    'note' => $note,
                    'exception' => $t,
                    'errorCode' => $t->getCode(),
                    'errorMessage' => $t->getMessage(),
                ]
            );
            throw $t;
        }
    }

    public function addNoteToProduct(string $productId, string $note, Context $context): ?array
    {
        // First, check if the product exists and is active
        $productCriteria = new Criteria([$productId]);
        $productCriteria->addFilter(new EqualsFilter('active', true));

        $product = $this->productRepository->search($productCriteria, $context)->first();
        if (null === $product) {
            return null;
        }

        // Create the product note
        return $this->createProductNoteWithValidation($productId, $note, $context);
    }

    public function updateNoteWithHistory(string $id, string $newNote, Context $context): ?string
    {
        // First, get the current note to preserve history
        $currentNote = $this->getProductNoteById($id, $context);
        if (null === $currentNote) {
            return null;
        }

        // Example only: Create a history record (if you have a note_history entity)
        $historyData = [
            'noteId' => $id,
            'oldNote' => $currentNote->getNote(),
            'newNote' => $newNote,
            'changedAt' => new DateTimeImmutable(),
        ];

        // Update the main note
        $updateData = [
            'id' => $id,
            'note' => $newNote,
            'updatedAt' => new DateTimeImmutable(),
        ];


        // Perform both operations in a single transaction
        $result = $this->productNoteRepository->upsert([$updateData], $context);

        // Example only: If you have a history entity, you'd also update it here
        // $this->noteHistoryRepository->upsert([$historyData], $context);

        return $result->getEventByEntityName(ProductNoteDefinition::ENTITY_NAME)?->getIds()[0] ?? null;

    }

    private function getProductNoteById(string $id, Context $context): ?ProductNoteEntity
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('product');

        /** @var ProductNoteEntity|null $productNote */
        $productNote = $this->productNoteRepository->search($criteria, $context)->first();

        return $productNote;
    }
}
