<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use Exception;

/**
 * Base exception for all domain layer exceptions
 */
abstract class DomainException extends Exception
{
}
