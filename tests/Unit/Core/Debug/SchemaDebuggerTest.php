<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Debug;

use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Core\Debug\SchemaDebugger;
use PHPUnit\Framework\TestCase;

use function str_repeat;

/**
 * @internal
 *
 * @coversDefaultClass \Inesta\Schemas\Core\Debug\SchemaDebugger
 */
final class SchemaDebuggerTest extends TestCase
{
    private SchemaDebugger $debugger;

    protected function setUp(): void
    {
        $this->debugger = new SchemaDebugger();
    }

    /**
     * @covers ::getDebugInfo
     */
    public function testGetDebugInfo(): void
    {
        $schema = SchemaFactory::create('Article', [
            'headline' => 'Test Article',
            'author' => 'Test Author',
        ]);

        $info = $this->debugger->getDebugInfo($schema);

        self::assertIsArray($info);
        self::assertArrayHasKey('type', $info);
        self::assertArrayHasKey('context', $info);
        self::assertArrayHasKey('class', $info);
        self::assertArrayHasKey('properties', $info);
        self::assertArrayHasKey('validation', $info);
        self::assertArrayHasKey('structure', $info);
        self::assertArrayHasKey('metadata', $info);

        self::assertSame('Article', $info['type']);
        self::assertSame('https://schema.org', $info['context']);
    }

    /**
     * @covers ::formatDebugInfo
     */
    public function testFormatDebugInfo(): void
    {
        $schema = SchemaFactory::create('Article', [
            'headline' => 'Test Article',
            'author' => 'Test Author',
        ]);

        $formatted = $this->debugger->formatDebugInfo($schema);

        self::assertIsString($formatted);
        self::assertStringContainsString('Schema Debug Information', $formatted);
        self::assertStringContainsString('Type: Article', $formatted);
        self::assertStringContainsString('Properties', $formatted);
        self::assertStringContainsString('headline: Test Article', $formatted);
        self::assertStringContainsString('Validation', $formatted);
        self::assertStringContainsString('Structure', $formatted);
    }

    /**
     * @covers ::analyzeRenderingPerformance
     */
    public function testAnalyzeRenderingPerformance(): void
    {
        $schema = SchemaFactory::create('Article', [
            'headline' => 'Performance Test',
            'author' => 'Tester',
        ]);

        $performance = $this->debugger->analyzeRenderingPerformance($schema);

        self::assertIsArray($performance);
        self::assertArrayHasKey('json-ld', $performance);
        self::assertArrayHasKey('microdata', $performance);
        self::assertArrayHasKey('rdfa', $performance);

        foreach ($performance as $format => $metrics) {
            self::assertArrayHasKey('time_ms', $metrics);
            self::assertArrayHasKey('size_bytes', $metrics);
            self::assertArrayHasKey('memory_used', $metrics);
            self::assertIsFloat($metrics['time_ms']);
            self::assertIsInt($metrics['size_bytes']);
            self::assertIsInt($metrics['memory_used']);
            self::assertGreaterThan(0, $metrics['size_bytes']);
        }
    }

    /**
     * @covers ::generateReport
     */
    public function testGenerateReport(): void
    {
        $schema = SchemaFactory::create('Person', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $report = $this->debugger->generateReport($schema);

        self::assertIsString($report);
        self::assertStringContainsString('Schema Debug Information', $report);
        self::assertStringContainsString('Rendering Performance', $report);
        self::assertStringContainsString('JSON-LD:', $report);
        self::assertStringContainsString('MICRODATA:', $report);
        self::assertStringContainsString('RDFA:', $report);
        self::assertStringContainsString('Time:', $report);
        self::assertStringContainsString('Size:', $report);
        self::assertStringContainsString('Memory:', $report);
    }

    /**
     * @covers ::getDebugInfo
     */
    public function testGetDebugInfoWithNestedSchemas(): void
    {
        $author = SchemaFactory::create('Person', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $schema = SchemaFactory::create('Article', [
            'headline' => 'Nested Schema Test',
            'author' => $author,
        ]);

        $info = $this->debugger->getDebugInfo($schema);

        self::assertSame(2, $info['structure']['property_count']);
        self::assertSame(1, $info['structure']['nested_count']);
        self::assertContains('Person', $info['structure']['nested_types']);
        self::assertSame(2, $info['structure']['max_depth']);

        // Check that nested schema is properly identified
        self::assertArrayHasKey('author', $info['properties']);
        self::assertStringContainsString('Person', $info['properties']['author']['type']);
    }

    /**
     * @covers ::getDebugInfo
     */
    public function testGetDebugInfoWithArrayProperties(): void
    {
        $schema = SchemaFactory::create('Article', [
            'headline' => 'Array Test',
            'keywords' => ['php', 'schema', 'testing'],
        ]);

        $info = $this->debugger->getDebugInfo($schema);

        self::assertArrayHasKey('keywords', $info['properties']);
        self::assertStringContainsString('Array (3 items)', $info['properties']['keywords']['type']);
    }

    /**
     * @covers ::getDebugInfo
     */
    public function testGetDebugInfoWithLongStringValues(): void
    {
        $longDescription = str_repeat('This is a very long description. ', 20);

        $schema = SchemaFactory::create('Article', [
            'headline' => 'Long Content Test',
            'description' => $longDescription,
        ]);

        $info = $this->debugger->getDebugInfo($schema);

        // Check that long strings are truncated in the debug output
        $formatted = $this->debugger->formatDebugInfo($schema);
        self::assertStringContainsString('...', $formatted);
    }

    /**
     * @covers ::getDebugInfo
     */
    public function testGetDebugInfoValidationErrors(): void
    {
        // Create a schema that should have validation errors
        $schema = SchemaFactory::create('Article', [
            'author' => 'Test Author',
            // Missing required 'headline' property
        ]);

        $info = $this->debugger->getDebugInfo($schema);

        self::assertFalse($info['validation']['valid']);
        self::assertGreaterThan(0, $info['validation']['error_count']);
        self::assertNotEmpty($info['validation']['errors']);
    }
}
