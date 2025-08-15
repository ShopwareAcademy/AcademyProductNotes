# Academy Product Notes Plugin

A Shopware 6 plugin that demonstrates how to use the Data Abstraction Layer (DAL) to create custom entities. This plugin adds a notes system for products that is accessible only in the admin panel.

## Features

- Custom entity `academy_product_note` with full DAL integration
- Admin interface for managing product notes
- Notes tab in product detail page
- Support for German and English translations
- Full CRUD operations (Create, Read, Update, Delete)
- User tracking and solved status for notes

## Entity Structure

The plugin creates a custom entity with the following fields:

- `id` - Primary key (UUID)
- `product_id` - Foreign key to product table
- `user_name` - Name of the user who created the note
- `note` - The note content (long text)
- `solved` - Boolean flag indicating if the note is resolved
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

## Database Schema

```sql
CREATE TABLE `academy_product_note` (
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
```

## Architecture Overview

### Core Components

1. **Entity Layer**
   - `ProductNoteEntity` - Entity class with getters/setters
   - `ProductNoteDefinition` - DAL field definitions
   - `ProductNoteCollection` - Collection class for multiple entities

2. **Extensions**
   - `ProductExtension` - Extends product entity with notes association

3. **Admin Interface**
   - Vue.js components for notes management
   - Integration with product detail page
   - Translations for German and English

4. **Migration**
   - Database table creation with proper constraints
   - Destructive migration for clean uninstall

### File Structure

```
src/
├── AcademyProductNotes.php                          # Main plugin class
├── Core/
│   └── Content/
│       ├── Product/
│       │   └── ProductExtension.php                 # Product entity extension
│       └── ProductNote/
│           ├── ProductNoteEntity.php                # Entity class
│           ├── ProductNoteDefinition.php            # DAL definition
│           └── ProductNoteCollection.php            # Collection class
├── Migration/
│   └── Migration1747313200CreateProductNoteTable.php # Database migration
└── Resources/
    ├── config/
    │   └── services.xml                             # Service definitions
    └── app/
        └── administration/
            └── src/
                ├── main.js                          # Entry point
                ├── snippet/                         # Translations
                │   ├── en-GB.json
                │   └── de-DE.json
                ├── module/
                │   └── sw-product/                  # Product module extension
                └── component/
                    └── academy-product-note-modal/  # Note modal component
```

## Installation

1. Place the plugin in `custom/plugins/AcademyProductNotes/`
2. Run `bin/console plugin:refresh`
3. Run `bin/console plugin:install --activate AcademyProductNotes`
4. Run `bin/console cache:clear`
5. Run `bin/build-administration.sh`

## Usage

1. Navigate to Products → [Select a Product] in the admin panel
2. Click on the "Notes" tab (German: "Notizen")
3. Use the "Add Note" button to create new notes
4. Edit or delete existing notes using the context menu
5. Mark notes as solved using the checkbox

## Technical Details

### DAL Integration

The plugin demonstrates proper DAL usage:

- **Entity Definition**: Defines fields, associations, and constraints
- **Entity Extension**: Extends existing product entity with new associations
- **Repository Usage**: Leverages Shopware's repository pattern for data access
- **Migrations**: Proper database schema management

### Admin Integration

- **Module Extension**: Extends existing product module with new tab
- **Vue Components**: Custom components following Shopware's admin patterns
- **Repository Integration**: Uses Shopware's admin repository system
- **Translations**: Multi-language support for admin interface

### Service Registration

All services are properly registered in `services.xml`:

- Entity definition with `shopware.entity.definition` tag
- Entity extension with `shopware.entity.extension` tag

## Development Notes

This plugin serves as a reference implementation for:

- Creating custom entities in Shopware 6
- Extending existing entities with associations
- Building admin interfaces for custom data
- Implementing proper migrations
- Following Shopware 6 development best practices

## License

MIT License 