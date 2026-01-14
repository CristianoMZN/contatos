<?php

declare(strict_types=1);

namespace App\Domain\Shared\Service;

use App\Domain\Shared\ValueObject\Slug;

/**
 * Slug Generator Domain Service
 * 
 * Generates unique slugs from strings
 */
final class SlugGenerator
{
    /**
     * Generate slug from string
     */
    public function generate(string $text): Slug
    {
        return Slug::fromString($text);
    }

    /**
     * Generate unique slug with suffix if needed
     * 
     * @param callable $existsChecker Callback to check if slug exists: fn(Slug): bool
     */
    public function generateUnique(string $text, callable $existsChecker): Slug
    {
        $baseSlug = Slug::fromString($text);

        if (!$existsChecker($baseSlug)) {
            return $baseSlug;
        }

        // Try with numeric suffixes
        $counter = 1;
        while ($counter < 100) {
            $suffixedSlug = Slug::fromString($text . '-' . $counter);

            if (!$existsChecker($suffixedSlug)) {
                return $suffixedSlug;
            }

            $counter++;
        }

        // If all suffixes are taken, add random string
        $randomSuffix = substr(md5(uniqid((string) mt_rand(), true)), 0, 6);
        return Slug::fromString($text . '-' . $randomSuffix);
    }

    /**
     * Suggest alternative slugs
     * 
     * @return Slug[]
     */
    public function suggestAlternatives(string $text, int $count = 5): array
    {
        $baseSlug = Slug::fromString($text);
        $suggestions = [$baseSlug];

        // Add numbered variations
        for ($i = 1; $i < $count; $i++) {
            $suggestions[] = Slug::fromString($text . '-' . $i);
        }

        return $suggestions;
    }
}
