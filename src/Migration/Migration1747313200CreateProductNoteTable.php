<?php declare(strict_types=1);

namespace AcademyProductNotes\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1747313200CreateProductNoteTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747313200;
    }

    /**
     * Update the database schema every time the plugin is updated
     *
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `academy_product_note` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `user_name` VARCHAR(255) NOT NULL,
    `note` LONGTEXT NOT NULL,
    `solved` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updated_at` DATETIME(3) NULL ON UPDATE CURRENT_TIMESTAMP(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.academy_product_note.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }
}
