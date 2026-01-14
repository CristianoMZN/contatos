<?php

declare(strict_types=1);

namespace App\Domain\Contact\Service;

use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\ValueObject\ContactId;
use App\Domain\Shared\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;

/**
 * Contact Duplicate Checker Domain Service
 * 
 * Verifies if a contact is a duplicate based on business rules
 */
final class ContactDuplicateChecker
{
    public function __construct(
        private ContactRepositoryInterface $repository
    ) {
    }

    /**
     * Check if email is already used by the user
     */
    public function isDuplicateEmail(
        UserId $userId,
        Email $email,
        ?ContactId $excludeContactId = null
    ): bool {
        $userContacts = $this->repository->findByUser($userId);

        foreach ($userContacts as $contact) {
            // Skip the contact being updated
            if ($excludeContactId && $contact->id()->equals($excludeContactId)) {
                continue;
            }

            if ($contact->email()->equals($email)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find similar contacts (fuzzy matching)
     * 
     * Returns contacts with similarity score >= threshold
     * 
     * @return array Array of ['contact' => Contact, 'similarity' => float]
     */
    public function findSimilar(
        UserId $userId,
        string $name,
        Email $email,
        float $threshold = 0.7
    ): array {
        $userContacts = $this->repository->findByUser($userId);
        $similar = [];

        foreach ($userContacts as $contact) {
            $similarity = $this->calculateSimilarity($contact, $name, $email);

            if ($similarity >= $threshold) {
                $similar[] = [
                    'contact' => $contact,
                    'similarity' => $similarity,
                ];
            }
        }

        // Sort by similarity (highest first)
        usort($similar, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return $similar;
    }

    /**
     * Calculate similarity score between 0 and 1
     */
    private function calculateSimilarity(
        $contact,
        string $name,
        Email $email
    ): float {
        $score = 0;

        // Email match (40% weight)
        if ($contact->email()->equals($email)) {
            $score += 0.4;
        }

        // Name similarity (60% weight using Levenshtein distance)
        $nameScore = $this->levenshteinNormalized($contact->name(), $name);
        $score += $nameScore * 0.6;

        return $score;
    }

    /**
     * Calculate normalized Levenshtein distance (0 = completely different, 1 = identical)
     */
    private function levenshteinNormalized(string $str1, string $str2): float
    {
        $str1 = strtolower($str1);
        $str2 = strtolower($str2);

        $maxLen = max(strlen($str1), strlen($str2));

        if ($maxLen === 0) {
            return 1.0;
        }

        $distance = levenshtein($str1, $str2);

        return 1 - ($distance / $maxLen);
    }
}
