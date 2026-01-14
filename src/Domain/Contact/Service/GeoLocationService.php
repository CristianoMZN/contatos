<?php

declare(strict_types=1);

namespace App\Domain\Contact\Service;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Shared\ValueObject\GeoLocation;

/**
 * GeoLocation Domain Service
 * 
 * Provides geo-spatial operations for contacts
 */
final class GeoLocationService
{
    /**
     * Find contacts near a specific location
     * 
     * @param Contact[] $contacts
     * @return array Array of ['contact' => Contact, 'distance' => float]
     */
    public function findNearby(
        array $contacts,
        GeoLocation $center,
        float $radiusKm
    ): array {
        $nearby = [];

        foreach ($contacts as $contact) {
            $location = $contact->location();

            if (!$location) {
                continue;
            }

            $distance = $location->distanceTo($center);

            if ($distance <= $radiusKm) {
                $nearby[] = [
                    'contact' => $contact,
                    'distance' => $distance,
                ];
            }
        }

        // Sort by distance (closest first)
        usort($nearby, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return $nearby;
    }

    /**
     * Calculate geographic center of multiple contacts
     * 
     * @param Contact[] $contacts
     */
    public function calculateCenter(array $contacts): ?GeoLocation
    {
        $locations = array_filter(
            array_map(fn(Contact $c) => $c->location(), $contacts)
        );

        if (empty($locations)) {
            return null;
        }

        $sumLat = 0;
        $sumLon = 0;

        foreach ($locations as $location) {
            $sumLat += $location->latitude();
            $sumLon += $location->longitude();
        }

        return GeoLocation::fromCoordinates(
            $sumLat / count($locations),
            $sumLon / count($locations)
        );
    }

    /**
     * Group contacts by proximity to a center point
     * 
     * @param Contact[] $contacts
     * @return array<string, Contact[]> Array grouped by distance range
     */
    public function groupByProximity(
        array $contacts,
        GeoLocation $center
    ): array {
        $groups = [
            'nearby' => [],      // < 5km
            'close' => [],       // 5-20km
            'moderate' => [],    // 20-50km
            'far' => [],         // > 50km
            'no_location' => [], // No location data
        ];

        foreach ($contacts as $contact) {
            $location = $contact->location();

            if (!$location) {
                $groups['no_location'][] = $contact;
                continue;
            }

            $distance = $location->distanceTo($center);

            if ($distance < 5) {
                $groups['nearby'][] = $contact;
            } elseif ($distance < 20) {
                $groups['close'][] = $contact;
            } elseif ($distance < 50) {
                $groups['moderate'][] = $contact;
            } else {
                $groups['far'][] = $contact;
            }
        }

        return $groups;
    }
}
