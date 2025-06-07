<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Compliance;

use PHPUnit\Framework\TestCase;

/**
 * Placeholder test for compliance test suite.
 *
 * This prevents PHPUnit from failing when the compliance directory is empty.
 * Real Schema.org compliance tests will be added in future versions.
 *
 * @internal
 *
 * @coversNothing
 */
final class PlaceholderTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testPlaceholder(): void
    {
        // This test exists only to prevent PHPUnit errors
        // when the compliance test suite is empty
        self::assertTrue(true);
    }
}
