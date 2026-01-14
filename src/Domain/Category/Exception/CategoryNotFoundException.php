<?php

declare(strict_types=1);

namespace App\Domain\Category\Exception;

use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Shared\Exception\DomainException;

/**
 * Exception thrown when a category is not found
 */
final class CategoryNotFoundException extends DomainException
{
    public static function withId(CategoryId $id): self
    {
        return new self(sprintf('Category with ID "%s" was not found', $id->value()));
    }
}
