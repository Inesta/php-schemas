<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Performance;

use Inesta\Schemas\Core\Performance\LazySchemaLoader;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Core\Performance\LazySchemaLoader
 *
 * @internal
 */
final class LazySchemaLoaderTest extends TestCase
{
    public function testSchemaIsNotLoadedInitially(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        self::assertFalse($loader->isLoaded());
    }

    public function testSchemaIsLoadedOnFirstAccess(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        $type = $loader->getType();

        self::assertTrue($loader->isLoaded());
        self::assertSame('Thing', $type);
    }

    public function testPropertiesAreDelegatedToActualSchema(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test', 'description' => 'A test thing']));

        self::assertSame(['name' => 'Test', 'description' => 'A test thing'], $loader->getProperties());
        self::assertSame('Test', $loader->getProperty('name'));
        self::assertTrue($loader->hasProperty('name'));
        self::assertFalse($loader->hasProperty('nonexistent'));
    }

    public function testWithPropertyReturnsNewLazyLoader(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        $newLoader = $loader->withProperty('description', 'Added description');

        self::assertNotSame($loader, $newLoader);
        self::assertInstanceOf(LazySchemaLoader::class, $newLoader);
        self::assertFalse($newLoader->isLoaded());
    }

    public function testValidationIsDelegated(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        $result = $loader->validate();

        self::assertTrue($result->isValid());
        self::assertTrue($loader->isValid());
    }

    public function testRenderingIsDelegated(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        $array = $loader->toArray();
        $jsonLd = $loader->toJsonLd();
        $microdata = $loader->toMicrodata();
        $rdfa = $loader->toRdfa();

        self::assertIsArray($array);
        self::assertIsString($jsonLd);
        self::assertIsString($microdata);
        self::assertIsString($rdfa);

        self::assertArrayHasKey('@type', $array);
        self::assertSame('Thing', $array['@type']);
        self::assertSame('Test', $array['name']);
    }

    public function testForceLoad(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test']));

        self::assertFalse($loader->isLoaded());

        $schema = $loader->load();

        self::assertTrue($loader->isLoaded());
        self::assertInstanceOf(Thing::class, $schema);
        self::assertSame('Test', $schema->getProperty('name'));
    }

    public function testContextIsDelegated(): void
    {
        $loader = new LazySchemaLoader(static fn () => new Thing(['name' => 'Test'], 'https://custom.schema.org'));

        self::assertSame('https://custom.schema.org', $loader->getContext());
    }
}
