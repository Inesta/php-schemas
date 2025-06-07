<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Debug;

use Exception;
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Core\Debug\ErrorCollector;
use Inesta\Schemas\Core\Exceptions\InvalidPropertyException;
use Inesta\Schemas\Core\Exceptions\SchemaException;
use Inesta\Schemas\Core\Exceptions\ValidationException;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;
use PHPUnit\Framework\TestCase;

use function json_decode;

/**
 * @internal
 *
 * @coversDefaultClass \Inesta\Schemas\Core\Debug\ErrorCollector
 */
final class ErrorCollectorTest extends TestCase
{
    private ErrorCollector $collector;

    protected function setUp(): void
    {
        $this->collector = new ErrorCollector();
    }

    /**
     * @covers ::collectError
     * @covers ::getErrorCount
     * @covers ::getErrors
     * @covers ::hasErrors
     */
    public function testCollectError(): void
    {
        self::assertFalse($this->collector->hasErrors());
        self::assertSame(0, $this->collector->getErrorCount());

        $error = new Exception('Test error');
        $schema = SchemaFactory::create('Article', ['headline' => 'Test']);

        $this->collector->collectError($error, $schema, ['test' => 'context']);

        self::assertTrue($this->collector->hasErrors());
        self::assertSame(1, $this->collector->getErrorCount());

        $errors = $this->collector->getErrors();
        self::assertCount(1, $errors);

        $collectedError = $errors[0];
        self::assertSame('Exception', $collectedError['type']);
        self::assertSame('Test error', $collectedError['message']);
        self::assertSame('Article', $collectedError['schema_type']);
        self::assertSame('https://schema.org', $collectedError['schema_context']);
        self::assertSame(['test' => 'context'], $collectedError['context']);
        self::assertArrayHasKey('suggestion', $collectedError);
        self::assertArrayHasKey('timestamp', $collectedError);
    }

    /**
     * @covers ::collectError
     */
    public function testCollectInvalidPropertyException(): void
    {
        $error = InvalidPropertyException::unknownProperty('invalidProp', 'Article', 'test value');
        $schema = SchemaFactory::create('Article', ['headline' => 'Test']);

        $this->collector->collectError($error, $schema);

        $errors = $this->collector->getErrors();
        $collectedError = $errors[0];

        self::assertSame('Inesta\Schemas\Core\Exceptions\InvalidPropertyException', $collectedError['type']);
        self::assertStringContainsString('property', $collectedError['suggestion']);
    }

    /**
     * @covers ::collectError
     */
    public function testCollectValidationException(): void
    {
        $validationResult = new ValidationResult([
            new ValidationError('headline', 'Headline is required', null, 'required'),
        ]);

        $error = new ValidationException($validationResult);
        $schema = SchemaFactory::create('Article', []);

        $this->collector->collectError($error, $schema);

        $errors = $this->collector->getErrors();
        $collectedError = $errors[0];

        self::assertSame('Inesta\Schemas\Core\Exceptions\ValidationException', $collectedError['type']);
        self::assertStringContainsString('validation', $collectedError['suggestion']);
    }

    /**
     * @covers ::collectError
     */
    public function testCollectSchemaException(): void
    {
        $error = SchemaException::unknownType('InvalidType');
        $schema = SchemaFactory::create('Article', ['headline' => 'Test']);

        $this->collector->collectError($error, $schema);

        $errors = $this->collector->getErrors();
        $collectedError = $errors[0];

        self::assertSame('Inesta\Schemas\Core\Exceptions\SchemaException', $collectedError['type']);
        self::assertStringContainsString('schema', $collectedError['suggestion']);
    }

    /**
     * @covers ::collectValidationErrors
     */
    public function testCollectValidationErrors(): void
    {
        $validationErrors = [
            new ValidationError('Headline is required', 'required', 'headline', null),
            new ValidationError('Author cannot be empty', 'empty', 'author', ''),
        ];

        $schema = SchemaFactory::create('Article', []);

        $this->collector->collectValidationErrors($validationErrors, $schema);

        self::assertSame(2, $this->collector->getErrorCount());

        $errors = $this->collector->getErrors();
        self::assertSame('ValidationError', $errors[0]['type']);
        self::assertSame('headline', $errors[0]['property'] ?? null);
        self::assertStringContainsString('property "headline" is required', $errors[0]['suggestion']);

        self::assertSame('ValidationError', $errors[1]['type']);
        self::assertSame('author', $errors[1]['property'] ?? null);
        self::assertStringContainsString('property "author" cannot be empty', $errors[1]['suggestion']);
    }

    /**
     * @covers ::clear
     */
    public function testClear(): void
    {
        $error = new Exception('Test error');
        $this->collector->collectError($error);

        self::assertTrue($this->collector->hasErrors());

        $this->collector->clear();

        self::assertFalse($this->collector->hasErrors());
        self::assertSame(0, $this->collector->getErrorCount());
        self::assertEmpty($this->collector->getErrors());
    }

    /**
     * @covers ::formatErrors
     */
    public function testFormatErrors(): void
    {
        $error1 = new Exception('First error');
        $error2 = InvalidPropertyException::unknownProperty('badProp', 'Article');

        $this->collector->collectError($error1);
        $this->collector->collectError($error2);

        $formatted = $this->collector->formatErrors();

        self::assertIsString($formatted);
        self::assertStringContainsString('Error Report (2 errors)', $formatted);
        self::assertStringContainsString('Error #1: Exception', $formatted);
        self::assertStringContainsString('Message: First error', $formatted);
        self::assertStringContainsString('Error #2: Inesta\Schemas\Core\Exceptions\InvalidPropertyException', $formatted);
    }

    /**
     * @covers ::formatErrors
     */
    public function testFormatErrorsWhenEmpty(): void
    {
        $formatted = $this->collector->formatErrors();
        self::assertSame('No errors collected.', $formatted);
    }

    /**
     * @covers ::getErrorsByType
     */
    public function testGetErrorsByType(): void
    {
        $this->collector->collectError(new Exception('Error 1'));
        $this->collector->collectError(new Exception('Error 2'));
        $this->collector->collectError(InvalidPropertyException::unknownProperty('prop', 'Type'));

        $grouped = $this->collector->getErrorsByType();

        self::assertArrayHasKey('Exception', $grouped);
        self::assertArrayHasKey('Inesta\Schemas\Core\Exceptions\InvalidPropertyException', $grouped);
        self::assertCount(2, $grouped['Exception']);
        self::assertCount(1, $grouped['Inesta\Schemas\Core\Exceptions\InvalidPropertyException']);
    }

    /**
     * @covers ::getErrorSummary
     */
    public function testGetErrorSummary(): void
    {
        $this->collector->collectError(new Exception('Error 1'));
        $this->collector->collectError(new Exception('Error 2'));
        $this->collector->collectError(InvalidPropertyException::unknownProperty('prop', 'Type'));

        $summary = $this->collector->getErrorSummary();

        self::assertSame(2, $summary['Exception']);
        self::assertSame(1, $summary['Inesta\Schemas\Core\Exceptions\InvalidPropertyException']);
    }

    /**
     * @covers ::exportAsJson
     */
    public function testExportAsJson(): void
    {
        $error = new Exception('Test error');
        $this->collector->collectError($error);

        $json = $this->collector->exportAsJson();

        self::assertIsString($json);
        $decoded = json_decode($json, true);
        self::assertIsArray($decoded);
        self::assertCount(1, $decoded);
        self::assertSame('Exception', $decoded[0]['type']);
        self::assertSame('Test error', $decoded[0]['message']);
    }

    /**
     * @covers ::collectError
     */
    public function testCollectErrorWithContext(): void
    {
        $error = new Exception('Context test');
        $context = [
            'user_input' => 'invalid data',
            'operation' => 'create_schema',
            'nested_data' => ['a', 'b', 'c'],
        ];

        $this->collector->collectError($error, null, $context);

        $errors = $this->collector->getErrors();
        $collectedError = $errors[0];

        self::assertSame($context, $collectedError['context']);
    }

    /**
     * @covers ::collectError
     */
    public function testCollectErrorWithoutSchema(): void
    {
        $error = new Exception('No schema error');

        $this->collector->collectError($error);

        $errors = $this->collector->getErrors();
        $collectedError = $errors[0];

        self::assertNull($collectedError['schema_type']);
        self::assertNull($collectedError['schema_context']);
    }
}
