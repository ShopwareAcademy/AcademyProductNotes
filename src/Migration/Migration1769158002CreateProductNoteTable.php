<?php declare(strict_types=1);

namespace AcademyProductNotes\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1769158002CreateProductNoteTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1769158002;
    }

    /**
     * Applies the database schema changes for this migration
     * This method is executed once when the migration is new.
     *
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `academy_product_note` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL, 
    `note` LONGTEXT NOT NULL,
    `solved` TINYINT(1) DEFAULT 0 NOT NULL,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` DATETIME(3) NULL ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.academy_product_note.product`
      FOREIGN KEY (`product_id`, `product_version_id`)
      REFERENCES `product` (`id`, `version_id`) 
      ON DELETE CASCADE
      ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }
}
