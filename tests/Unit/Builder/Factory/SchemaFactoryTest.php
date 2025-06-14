<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Builder\Factory;

use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Exceptions\SchemaException;
use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Organization;
use Inesta\Schemas\Core\Types\Person;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @covers \Inesta\Schemas\Builder\Factory\SchemaFactory
 *
 * @internal
 */
final class SchemaFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        SchemaFactory::resetRegistry();
    }

    protected function tearDown(): void
    {
        SchemaFactory::resetRegistry();
        parent::tearDown();
    }

    public function testCanCreateThingType(): void
    {
        $schema = SchemaFactory::create('Thing', ['name' => 'Test']);

        self::assertInstanceOf(Thing::class, $schema);
        self::assertSame('Thing', $schema->getType());
        self::assertSame('Test', $schema->getProperty('name'));
        self::assertSame('https://schema.org', $schema->getContext());
    }

    public function testCanCreateArticleType(): void
    {
        $schema = SchemaFactory::create('Article', ['headline' => 'Test Article']);

        self::assertInstanceOf(Article::class, $schema);
        self::assertSame('Article', $schema->getType());
        self::assertSame('Test Article', $schema->getProperty('headline'));
        self::assertSame('https://schema.org', $schema->getContext());
    }

    public function testCanCreatePersonType(): void
    {
        $schema = SchemaFactory::create('Person', ['name' => 'John Doe']);

        self::assertInstanceOf(Person::class, $schema);
        self::assertSame('Person', $schema->getType());
        self::assertSame('John Doe', $schema->getProperty('name'));
        self::assertSame('https://schema.org', $schema->getContext());
    }

    public function testCanCreateOrganizationType(): void
    {
        $schema = SchemaFactory::create('Organization', ['name' => 'Acme Corp']);

        self::assertInstanceOf(Organization::class, $schema);
        self::assertSame('Organization', $schema->getType());
        self::assertSame('Acme Corp', $schema->getProperty('name'));
        self::assertSame('https://schema.org', $schema->getContext());
    }

    public function testCanCreateWithCustomContext(): void
    {
        $context = 'https://example.com/context';
        $schema = SchemaFactory::create('Thing', [], $context);

        self::assertSame($context, $schema->getContext());
    }

    public function testThrowsExceptionForUnregisteredType(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Schema type "UnknownType" is not registered');

        SchemaFactory::create('UnknownType');
    }

    public function testCanRegisterNewType(): void
    {
        $thingClass = Thing::class;
        SchemaFactory::registerType('CustomType', $thingClass);

        self::assertTrue(SchemaFactory::hasType('CustomType'));
        self::assertSame($thingClass, SchemaFactory::getTypeClass('CustomType'));
    }

    public function testRegisterTypeThrowsExceptionForNonExistentClass(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Class "NonExistentClass" does not exist');

        /** @var class-string<SchemaTypeInterface> $invalidClass */
        $invalidClass = 'NonExistentClass';
        SchemaFactory::registerType('CustomType', $invalidClass);
    }

    public function testRegisterTypeThrowsExceptionForInvalidClass(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Class "stdClass" must implement');

        /** @var class-string<SchemaTypeInterface> $invalidClass */
        $invalidClass = stdClass::class;
        SchemaFactory::registerType('CustomType', $invalidClass);
    }

    public function testHasTypeReturnsTrueForRegisteredTypes(): void
    {
        self::assertTrue(SchemaFactory::hasType('Thing'));
        self::assertFalse(SchemaFactory::hasType('UnknownType'));
    }

    public function testGetRegisteredTypesReturnsAllTypes(): void
    {
        $types = SchemaFactory::getRegisteredTypes();

        self::assertArrayHasKey('Thing', $types);
        self::assertSame(Thing::class, $types['Thing']);
    }

    public function testGetTypeClassThrowsExceptionForUnregisteredType(): void
    {
        $this->expectException(SchemaException::class);
        $this->expectExceptionMessage('Schema type "UnknownType" is not registered');

        SchemaFactory::getTypeClass('UnknownType');
    }

    public function testClearRegistryClearsAllTypes(): void
    {
        SchemaFactory::clearRegistry();

        self::assertEmpty(SchemaFactory::getRegisteredTypes());
        self::assertFalse(SchemaFactory::hasType('Thing'));
    }

    public function testResetRegistryRestoresDefaultTypes(): void
    {
        SchemaFactory::clearRegistry();
        self::assertEmpty(SchemaFactory::getRegisteredTypes());

        SchemaFactory::resetRegistry();
        self::assertTrue(SchemaFactory::hasType('Thing'));
        self::assertTrue(SchemaFactory::hasType('Article'));
        self::assertTrue(SchemaFactory::hasType('Person'));
        self::assertTrue(SchemaFactory::hasType('Organization'));
    }
}
