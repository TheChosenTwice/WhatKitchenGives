<?php

namespace App\Models;

use Framework\Core\Model;

class Ingredient extends Model
{
    // DB columns must match these protected properties
    protected ?int $id = null;
    protected string $name;
    // New column introduced in DB migration: category_id
    protected ?int $category_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    // Returns the name of the related Category, if available.
    public function getCategory(): ?string
    {
        // First try to resolve via ResultSet (bulk-loaded relation)
        try {
            $cat = $this->getOneRelated(Category::class, 'category_id');
        } catch (\Throwable $e) {
            $cat = null;
        }

        // Fallback: if not found in ResultSet, try direct DB lookup by FK
        if ($cat === null && $this->category_id !== null) {
            try {
                $cat = Category::getOne($this->category_id);
            } catch (\Throwable $e) {
                $cat = null;
            }
        }

        if ($cat === null) {
            return null;
        }

        // Use public getter only (category name property is protected)
        return method_exists($cat, 'getName') ? $cat->getName() : null;
    }
}
