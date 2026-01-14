<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Domain\Contact\Entity\Contact;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Twig component responsible for rendering a contact card or row.
 */
#[AsTwigComponent('contact_card')]
final class ContactCardComponent
{
    public Contact $contact;

    /**
     * Display mode (grid|list) to adjust layout.
     */
    public string $viewMode = 'grid';

    public function displayMode(): string
    {
        return in_array($this->viewMode, ['grid', 'list'], true) ? $this->viewMode : 'grid';
    }

    public function initials(): string
    {
        $parts = explode(' ', $this->contact->name());
        $initials = '';

        foreach ($parts as $part) {
            if ($part !== '') {
                $initials .= mb_strtoupper(mb_substr($part, 0, 1));
            }

            if (mb_strlen($initials) >= 2) {
                break;
            }
        }

        return $initials !== '' ? $initials : 'C';
    }

    public function phone(): ?string
    {
        return $this->contact->phone()?->formatted();
    }

    public function location(): ?string
    {
        $location = $this->contact->location();

        if (!$location) {
            return null;
        }

        return sprintf('%.4f, %.4f', $location->latitude(), $location->longitude());
    }
}
