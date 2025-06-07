<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Builder;

use Inesta\Schemas\Builder\AbstractBuilder;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Builder\AbstractBuilder
 *
 * @internal
 */
final class AbstractBuilderTest extends TestCase
{
    private TestableBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new TestableBuilder();
    }

    public function testInitialState(): void
    {
        self::assertSame([], $this->builder->getData());
        self::assertSame('https://schema.org', $this->builder->getContext());
    }

    public function testReset(): void
    {
        $this->builder->setTestProperty('name', 'Test');
        $this->builder->setContext('https://custom.context');

        $result = $this->builder->reset();

        self::assertSame($this->builder, $result);
        self::assertSame([], $this->builder->getData());
        self::assertSame('https://schema.org', $this->builder->getContext());
    }

    public function testSetAndGetContext(): void
    {
        $context = 'https://custom.context.com';
        $result = $this->builder->setContext($context);

        self::assertSame($this->builder, $result);
        self::assertSame($context, $this->builder->getContext());
    }

    public function testSetProperty(): void
    {
        $this->builder->setTestProperty('name', 'Test Name');

        self::assertSame(['name' => 'Test Name'], $this->builder->getData());
    }

    public function testGetProperty(): void
    {
        $this->builder->setTestProperty('name', 'Test Name');

        self::assertSame('Test Name', $this->builder->getTestProperty('name'));
        self::assertNull($this->builder->getTestProperty('nonexistent'));
        self::assertSame('default', $this->builder->getTestProperty('nonexistent', 'default'));
    }

    public function testHasProperty(): void
    {
        $this->builder->setTestProperty('name', 'Test Name');

        self::assertTrue($this->builder->hasTestProperty('name'));
        self::assertFalse($this->builder->hasTestProperty('nonexistent'));
    }

    public function testRemoveProperty(): void
    {
        $this->builder->setTestProperty('name', 'Test Name');
        $this->builder->setTestProperty('description', 'Test Description');

        $result = $this->builder->removeTestProperty('name');

        self::assertSame($this->builder, $result);
        self::assertFalse($this->builder->hasTestProperty('name'));
        self::assertTrue($this->builder->hasTestProperty('description'));
        self::assertSame(['description' => 'Test Description'], $this->builder->getData());
    }

    public function testAddToPropertyCreatesArray(): void
    {
        $this->builder->addToTestProperty('tags', 'tag1');
        $this->builder->addToTestProperty('tags', 'tag2');

        self::assertSame(['tags' => ['tag1', 'tag2']], $this->builder->getData());
    }

    public function testAddToPropertyConvertsScalarToArray(): void
    {
        $this->builder->setTestProperty('tags', 'single-tag');
        $this->builder->addToTestProperty('tags', 'tag2');

        self::assertSame(['tags' => ['single-tag', 'tag2']], $this->builder->getData());
    }

    public function testAddToPropertyAppendsToExistingArray(): void
    {
        $this->builder->setTestProperty('tags', ['tag1']);
        $this->builder->addToTestProperty('tags', 'tag2');

        self::assertSame(['tags' => ['tag1', 'tag2']], $this->builder->getData());
    }

    public function testMergeData(): void
    {
        $this->builder->setTestProperty('name', 'Original Name');
        $this->builder->mergeTestData(['name' => 'New Name', 'description' => 'Test Description']);

        self::assertSame([
            'name' => 'New Name',
            'description' => 'Test Description',
        ], $this->builder->getData());
    }

    public function testBuild(): void
    {
        $this->builder->setTestProperty('name', 'Test Thing');
        $result = $this->builder->build();

        self::assertInstanceOf(Thing::class, $result);
        self::assertSame('Test Thing', $result->getProperty('name'));
        self::assertSame('https://schema.org', $result->getContext());
    }

    public function testBuildWithCustomContext(): void
    {
        $this->builder->setContext('https://custom.context');
        $this->builder->setTestProperty('name', 'Test Thing');
        $result = $this->builder->build();

        self::assertInstanceOf(Thing::class, $result);
        self::assertSame('https://custom.context', $result->getContext());
    }

    public function testValidateData(): void
    {
        self::assertTrue($this->builder->testValidateData());
    }
}

/**
 * Testable implementation of AbstractBuilder for testing.
 */
final class TestableBuilder extends AbstractBuilder
{
    public function build(): SchemaTypeInterface
    {
        return new Thing($this->data, $this->context);
    }

    // Expose protected methods for testing
    public function setTestProperty(string $property, mixed $value): static
    {
        return $this->setProperty($property, $value);
    }

    public function getTestProperty(string $property, mixed $default = null): mixed
    {
        return $this->getProperty($property, $default);
    }

    public function hasTestProperty(string $property): bool
    {
        return $this->hasProperty($property);
    }

    public function removeTestProperty(string $property): static
    {
        return $this->removeProperty($property);
    }

    public function addToTestProperty(string $property, mixed $value): static
    {
        return $this->addToProperty($property, $value);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function mergeTestData(array $data): static
    {
        return $this->mergeData($data);
    }

    public function testValidateData(): bool
    {
        return $this->validateData();
    }

    protected function getSchemaClass(): string
    {
        return Thing::class;
    }
}
