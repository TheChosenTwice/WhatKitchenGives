<?php

namespace App\Models;

use Framework\Core\Model;

class Category extends Model
{
    // DB columns must match these protected properties
    protected ?int $id = null;
    protected string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
