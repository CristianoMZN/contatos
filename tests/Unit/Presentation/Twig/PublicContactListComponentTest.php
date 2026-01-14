<?php

declare(strict_types=1);

namespace Tests\Unit\Presentation\Twig;

use App\Application\UseCase\Contact\DTO\ContactListFilter;
use App\Application\UseCase\Contact\ListPublicContactsUseCase;
use App\Presentation\Twig\Components\PublicContactListComponent;
use PHPUnit\Framework\TestCase;

final class PublicContactListComponentTest extends TestCase
{
    public function test_builds_filter_from_live_props(): void
    {
        $useCase = $this->createMock(ListPublicContactsUseCase::class);
        $useCase->expects(self::once())
            ->method('execute')
            ->with(self::callback(function (ContactListFilter $filter): bool {
                self::assertSame('john', $filter->search);
                self::assertSame('business', $filter->categoryId);
                self::assertSame(10, $filter->limit);
                self::assertSame(12.34, $filter->latitude);
                self::assertSame(56.78, $filter->longitude);
                self::assertSame(15.0, $filter->radiusKm);

                return true;
            }))
            ->willReturn([]);

        $component = new PublicContactListComponent($useCase);
        $component->search = 'john';
        $component->categoryId = 'business';
        $component->limit = 10;
        $component->latitude = 12.34;
        $component->longitude = 56.78;
        $component->radiusKm = 15.0;

        $contacts = $component->getContacts();

        self::assertSame([], $contacts);
    }

    public function test_sanitized_view_mode_defaults_to_grid(): void
    {
        $useCase = $this->createMock(ListPublicContactsUseCase::class);
        $useCase->method('execute')->willReturn([]);

        $component = new PublicContactListComponent($useCase);
        $component->viewMode = 'unsupported';

        self::assertSame('grid', $component->sanitizedViewMode());
    }
}
