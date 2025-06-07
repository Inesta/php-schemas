<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Performance;

use Inesta\Schemas\Core\Performance\SchemaCache;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Core\Performance\SchemaCache
 *
 * @internal
 */
final class SchemaCacheTest extends TestCase
{
    protected function setUp(): void
    {
        SchemaCache::clear();
    }

    public function testGetReturnsNullForNonExistentKey(): void
    {
        $result = SchemaCache::get('Thing', ['name' => 'Test']);

        self::assertNull($result);
    }

    public function testPutAndGetStoresAndRetrievesSchema(): void
    {
        $schema = new Thing(['name' => 'Test']);
        $properties = ['name' => 'Test'];

        SchemaCache::put('Thing', $properties, 'https://schema.org', $schema);
        $retrieved = SchemaCache::get('Thing', $properties, 'https://schema.org');

        self::assertSame($schema, $retrieved);
    }

    public function testCacheHitIncreasesHitCount(): void
    {
        $schema = new Thing(['name' => 'Test']);
        $properties = ['name' => 'Test'];

        SchemaCache::put('Thing', $properties, 'https://schema.org', $schema);
        SchemaCache::get('Thing', $properties, 'https://schema.org');

        $stats = SchemaCache::getStats();
        self::assertSame(1, $stats['hits']);
        self::assertSame(0, $stats['misses']);
    }

    public function testCacheMissIncreasesMissCount(): void
    {
        SchemaCache::get('Thing', ['name' => 'Test']);

        $stats = SchemaCache::getStats();
        self::assertSame(0, $stats['hits']);
        self::assertSame(1, $stats['misses']);
    }

    public function testClearResetsAllCounters(): void
    {
        $schema = new Thing(['name' => 'Test']);
        SchemaCache::put('Thing', ['name' => 'Test'], 'https://schema.org', $schema);
        SchemaCache::get('Thing', ['name' => 'Test']);
        SchemaCache::get('Thing', ['name' => 'NonExistent']);

        SchemaCache::clear();
        $stats = SchemaCache::getStats();

        self::assertSame(0, $stats['hits']);
        self::assertSame(0, $stats['misses']);
        self::assertSame(0, $stats['size']);
    }

    public function testMaxSizeEnforcesLimit(): void
    {
        SchemaCache::setMaxSize(2);

        $schema1 = new Thing(['name' => 'Test1']);
        $schema2 = new Thing(['name' => 'Test2']);
        $schema3 = new Thing(['name' => 'Test3']);

        SchemaCache::put('Thing', ['name' => 'Test1'], 'https://schema.org', $schema1);
        SchemaCache::put('Thing', ['name' => 'Test2'], 'https://schema.org', $schema2);
        SchemaCache::put('Thing', ['name' => 'Test3'], 'https://schema.org', $schema3);

        $stats = SchemaCache::getStats();
        self::assertLessThanOrEqual(2, $stats['size']);

        // First item should be evicted
        $retrieved = SchemaCache::get('Thing', ['name' => 'Test1']);
        self::assertNull($retrieved);
    }

    public function testHitRatioCalculation(): void
    {
        $schema = new Thing(['name' => 'Test']);
        SchemaCache::put('Thing', ['name' => 'Test'], 'https://schema.org', $schema);

        // 2 hits, 1 miss
        SchemaCache::get('Thing', ['name' => 'Test']);
        SchemaCache::get('Thing', ['name' => 'Test']);
        SchemaCache::get('Thing', ['name' => 'NonExistent']);

        $stats = SchemaCache::getStats();
        self::assertSame(2, $stats['hits']);
        self::assertSame(1, $stats['misses']);
        self::assertEqualsWithDelta(0.667, $stats['hit_ratio'], 0.001);
    }
}
