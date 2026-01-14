<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidArgumentException;

/**
 * Slug Value Object
 * 
 * Immutable value object representing a URL-friendly slug
 */
final readonly class Slug
{
    private const MAX_LENGTH = 100;
    private const PATTERN = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    /**
     * Create Slug from string
     */
    public static function fromString(string $value): self
    {
        // Remove accents and convert to lowercase
        $slug = self::removeAccents($value);
        $slug = strtolower($slug);

        // Replace spaces and special characters with hyphens
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Remove leading and trailing hyphens
        $slug = trim($slug, '-');

        return new self($slug);
    }

    /**
     * Validate slug format
     */
    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Slug cannot be empty');
        }

        if (strlen($this->value) > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                sprintf('Slug cannot exceed %d characters', self::MAX_LENGTH)
            );
        }

        if (!preg_match(self::PATTERN, $this->value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid slug format: %s', $this->value)
            );
        }
    }

    /**
     * Remove accents from string
     */
    private static function removeAccents(string $string): string
    {
        $unwanted = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N',
        ];

        return strtr($string, $unwanted);
    }

    /**
     * Get the slug value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Compare with another Slug
     */
    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
