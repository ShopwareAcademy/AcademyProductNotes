<?php declare(strict_types=1);

namespace AcademyProductNotes\Core\Content\ProductNote;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductNoteEntity extends Entity
{
    use EntityIdTrait;

    protected string $productId;
    protected string $userName;
    protected string $note;
    protected bool $solved;

    protected ?ProductEntity $product = null;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function isSolved(): bool
    {
        return $this->solved;
    }

    public function setSolved(bool $solved): void
    {
        $this->solved = $solved;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }
} 