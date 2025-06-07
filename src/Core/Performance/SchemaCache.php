<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Performance;

use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function array_key_exists;
use function array_key_first;
use function count;
use function max;
use function md5;
use function serialize;

/**
 * High-performance cache for schema objects to avoid redundant creation.
 *
 * Implements flyweight pattern for commonly used schema instances.
 */
final class SchemaCache
{
    /** @var array<string, SchemaTypeInterface> */
    private static array $cache = [];

    private static int $maxSize = 1000;

    private static int $hits = 0;

    private static int $misses = 0;

    /**
     * Get a cached schema instance or create and cache a new one.
     *
     * @param string               $type       The schema type
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The schema context
     */
    public static function get(string $type, array $properties = [], string $context = 'https://schema.org'): ?SchemaTypeInterface
    {
        $key = self::generateKey($type, $properties, $context);

        if (array_key_exists($key, self::$cache)) {
            ++self::$hits;

            return self::$cache[$key];
        }

        ++self::$misses;

        return null;
    }

    /**
     * Store a schema instance in the cache.
     *
     * @param string               $type       The schema type
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The schema context
     * @param SchemaTypeInterface  $schema     The schema instance to cache
     */
    public static function put(string $type, array $properties, string $context, SchemaTypeInterface $schema): void
    {
        $key = self::generateKey($type, $properties, $context);

        // Implement LRU eviction if cache is full
        if (count(self::$cache) >= self::$maxSize) {
            // Remove first (oldest) item
            $firstKey = array_key_first(self::$cache);
            if ($firstKey !== null) {
                unset(self::$cache[$firstKey]);
            }
        }

        self::$cache[$key] = $schema;
    }

    /**
     * Clear the entire cache.
     */
    public static function clear(): void
    {
        self::$cache = [];
        self::$hits = 0;
        self::$misses = 0;
    }

    /**
     * Get cache statistics.
     *
     * @return array{hits: int, misses: int, size: int, hit_ratio: float}
     */
    public static function getStats(): array
    {
        $total = self::$hits + self::$misses;

        return [
            'hits' => self::$hits,
            'misses' => self::$misses,
            'size' => count(self::$cache),
            'hit_ratio' => $total > 0 ? self::$hits / $total : 0.0,
        ];
    }

    /**
     * Set the maximum cache size.
     */
    public static function setMaxSize(int $maxSize): void
    {
        self::$maxSize = max(1, $maxSize);
    }

    /**
     * Generate a cache key for the given parameters.
     *
     * @param string               $type       The schema type
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The schema context
     */
    private static function generateKey(string $type, array $properties, string $context): string
    {
        return md5($type . '|' . serialize($properties) . '|' . $context);
    }
}
