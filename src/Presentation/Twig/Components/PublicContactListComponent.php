<?php

declare(strict_types=1);

namespace App\Presentation\Twig\Components;

use App\Application\UseCase\Contact\DTO\ContactListFilter;
use App\Application\UseCase\Contact\ListPublicContactsUseCase;
use App\Domain\Contact\Entity\Contact;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Live component responsible for rendering and filtering the public contact list.
 */
#[AsLiveComponent('public_contact_list')]
final class PublicContactListComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $search = '';

    #[LiveProp(writable: true)]
    public ?string $categoryId = null;

    #[LiveProp(writable: true)]
    public ?float $latitude = null;

    #[LiveProp(writable: true)]
    public ?float $longitude = null;

    #[LiveProp(writable: true)]
    public ?float $radiusKm = null;

    #[LiveProp(writable: true)]
    public string $viewMode = 'grid';

    #[LiveProp(writable: true)]
    public int $limit = 20;

    public function __construct(
        private readonly ListPublicContactsUseCase $listPublicContactsUseCase
    ) {
    }

    /**
     * @return Contact[]
     */
    public function getContacts(): array
    {
        $filter = new ContactListFilter(
            categoryId: $this->categoryId !== null && $this->categoryId !== '' ? $this->categoryId : null,
            search: $this->search !== '' ? $this->search : null,
            latitude: $this->latitude,
            longitude: $this->longitude,
            radiusKm: $this->radiusKm,
            limit: $this->limit,
            offset: 0,
            cursor: null
        );

        return $this->listPublicContactsUseCase->execute($filter);
    }

    public function sanitizedViewMode(): string
    {
        return in_array($this->viewMode, ['grid', 'list'], true) ? $this->viewMode : 'grid';
    }

    public function hasGeoFilter(): bool
    {
        return $this->latitude !== null && $this->longitude !== null && $this->radiusKm !== null;
    }
}
