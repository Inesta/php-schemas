<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Performance;

use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function memory_get_peak_usage;
use function memory_get_usage;
use function microtime;

/**
 * Performance benchmarking tools for schema operations.
 */
final class SchemaBenchmark
{
    /**
     * Benchmark schema creation performance.
     *
     * @param int                  $iterations Number of schemas to create
     * @param string               $type       Schema type to benchmark
     * @param array<string, mixed> $properties Properties for each schema
     *
     * @return array{time: float, memory: int, peak_memory: int, schemas_per_second: float}
     */
    public static function benchmarkCreation(int $iterations, string $type = 'Thing', array $properties = []): array
    {
        $factory = new SchemaFactory();
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        for ($i = 0; $i < $iterations; ++$i) {
            $factory->create($type, $properties);
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        $peakMemory = memory_get_peak_usage();

        $totalTime = $endTime - $startTime;

        return [
            'time' => $totalTime,
            'memory' => $endMemory - $startMemory,
            'peak_memory' => $peakMemory,
            'schemas_per_second' => $iterations / $totalTime,
        ];
    }

    /**
     * Benchmark rendering performance.
     *
     * @param SchemaTypeInterface $schema     Schema to render
     * @param int                 $iterations Number of renders to perform
     * @param string              $format     Rendering format (jsonld, microdata, rdfa)
     *
     * @return array{time: float, memory: int, renders_per_second: float}
     */
    public static function benchmarkRendering(SchemaTypeInterface $schema, int $iterations, string $format = 'jsonld'): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        for ($i = 0; $i < $iterations; ++$i) {
            match ($format) {
                'jsonld' => $schema->toJsonLd(),
                'microdata' => $schema->toMicrodata(),
                'rdfa' => $schema->toRdfa(),
                default => $schema->toJsonLd(),
            };
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $totalTime = $endTime - $startTime;

        return [
            'time' => $totalTime,
            'memory' => $endMemory - $startMemory,
            'renders_per_second' => $iterations / $totalTime,
        ];
    }

    /**
     * Benchmark validation performance.
     *
     * @param SchemaTypeInterface $schema     Schema to validate
     * @param int                 $iterations Number of validations to perform
     *
     * @return array{time: float, memory: int, validations_per_second: float}
     */
    public static function benchmarkValidation(SchemaTypeInterface $schema, int $iterations): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        for ($i = 0; $i < $iterations; ++$i) {
            $schema->validate();
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $totalTime = $endTime - $startTime;

        return [
            'time' => $totalTime,
            'memory' => $endMemory - $startMemory,
            'validations_per_second' => $iterations / $totalTime,
        ];
    }

    /**
     * Benchmark cache performance.
     *
     * @param int                  $iterations Number of cache operations
     * @param string               $type       Schema type to cache
     * @param array<string, mixed> $properties Schema properties
     *
     * @return array{cache_stats: array<string, mixed>, creation_time: float, cached_time: float, speedup: float}
     */
    public static function benchmarkCache(int $iterations, string $type = 'Thing', array $properties = []): array
    {
        $factory = new SchemaFactory();

        // Clear cache and benchmark creation without cache
        SchemaCache::clear();
        $startTime = microtime(true);

        for ($i = 0; $i < $iterations; ++$i) {
            $factory->create($type, $properties);
        }

        $creationTime = microtime(true) - $startTime;

        // Benchmark with cache
        SchemaCache::clear();
        $startTime = microtime(true);

        for ($i = 0; $i < $iterations; ++$i) {
            $cached = SchemaCache::get($type, $properties);
            if ($cached === null) {
                $schema = $factory->create($type, $properties);
                SchemaCache::put($type, $properties, 'https://schema.org', $schema);
            }
        }

        $cachedTime = microtime(true) - $startTime;

        return [
            'cache_stats' => SchemaCache::getStats(),
            'creation_time' => $creationTime,
            'cached_time' => $cachedTime,
            'speedup' => $creationTime > 0 ? $creationTime / $cachedTime : 1.0,
        ];
    }

    /**
     * Run a comprehensive performance test suite.
     *
     * @return array<string, mixed>
     */
    public static function runFullBenchmark(): array
    {
        $factory = new SchemaFactory();

        // Create test schemas
        $simpleSchema = $factory->create('Thing', ['name' => 'Test']);
        $complexSchema = $factory->create('Article', [
            'headline' => 'Test Article',
            'author' => ['name' => 'Test Author'],
            'datePublished' => '2024-01-01',
            'articleBody' => 'This is a test article for benchmarking.',
        ]);

        return [
            'creation' => [
                'simple' => self::benchmarkCreation(1000, 'Thing', ['name' => 'Test']),
                'complex' => self::benchmarkCreation(1000, 'Article', [
                    'headline' => 'Test Article',
                    'author' => ['name' => 'Test Author'],
                    'datePublished' => '2024-01-01',
                ]),
            ],
            'rendering' => [
                'jsonld' => self::benchmarkRendering($complexSchema, 1000, 'jsonld'),
                'microdata' => self::benchmarkRendering($complexSchema, 1000, 'microdata'),
                'rdfa' => self::benchmarkRendering($complexSchema, 1000, 'rdfa'),
            ],
            'validation' => [
                'simple' => self::benchmarkValidation($simpleSchema, 1000),
                'complex' => self::benchmarkValidation($complexSchema, 1000),
            ],
            'cache' => self::benchmarkCache(1000, 'Thing', ['name' => 'Test']),
        ];
    }
}
