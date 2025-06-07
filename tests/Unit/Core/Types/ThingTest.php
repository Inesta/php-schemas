<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Types;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Inesta\Schemas\Core\Types\Thing;
use Inesta\Schemas\Validation\ValidationResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Core\Types\Thing
 *
 * @internal
 */
final class ThingTest extends TestCase
{
    public function testItShouldCreateThingWithDefaultProperties(): void
    {
        $thing = new Thing();

        self::assertSame('Thing', $thing->getType());
        self::assertSame('https://schema.org', $thing->getContext());
        self::assertSame([], $thing->getProperties());
    }

    public function testItShouldCreateThingWithProperties(): void
    {
        $properties = [
            'name' => 'Test Thing',
            'description' => 'A test thing',
            'url' => 'https://example.com',
        ];

        $thing = new Thing($properties);

        self::assertSame($properties, $thing->getProperties());
        self::assertSame('Test Thing', $thing->getProperty('name'));
        self::assertSame('A test thing', $thing->getProperty('description'));
        self::assertSame('https://example.com', $thing->getProperty('url'));
    }

    public function testItShouldCheckIfPropertyExists(): void
    {
        $thing = new Thing(['name' => 'Test Thing']);

        self::assertTrue($thing->hasProperty('name'));
        self::assertFalse($thing->hasProperty('description'));
    }

    public function testItShouldReturnNullForNonExistentProperty(): void
    {
        $thing = new Thing();

        self::assertNull($thing->getProperty('nonexistent'));
    }

    public function testItShouldCreateNewInstanceWhenSettingProperty(): void
    {
        $original = new Thing(['name' => 'Original']);
        $modified = $original->withProperty('description', 'Added description');

        self::assertNotSame($original, $modified);
        self::assertSame('Original', $original->getProperty('name'));
        self::assertNull($original->getProperty('description'));

        self::assertSame('Original', $modified->getProperty('name'));
        self::assertSame('Added description', $modified->getProperty('description'));
    }

    public function testItShouldConvertToArrayWithContextAndType(): void
    {
        $thing = new Thing([
            'name' => 'Test Thing',
            'description' => 'A test thing',
        ]);

        $expected = [
            '@context' => 'https://schema.org',
            '@type' => 'Thing',
            'name' => 'Test Thing',
            'description' => 'A test thing',
        ];

        self::assertSame($expected, $thing->toArray());
    }

    public function testItShouldValidateSuccessfullyWithoutRequiredProperties(): void
    {
        $thing = new Thing();

        $result = $thing->validate();

        /** @phpstan-ignore-next-line */
        self::assertInstanceOf(ValidationResult::class, $result);
        self::assertTrue($result->isValid());
        self::assertFalse($result->hasErrors());
        self::assertTrue($thing->isValid());
    }

    public function testItShouldConvertToJsonLd(): void
    {
        $thing = new Thing(['name' => 'Test Thing']);

        $jsonLd = $thing->toJsonLd();

        /** @phpstan-ignore-next-line */
        self::assertIsString($jsonLd);
        self::assertJsonStringEqualsJsonString(
            '{"@context":"https://schema.org","@type":"Thing","name":"Test Thing"}',
            $jsonLd,
        );
    }

    public function testItShouldConvertToMicrodata(): void
    {
        $thing = new Thing(['name' => 'Test Thing']);

        $microdata = $thing->toMicrodata();

        /** @phpstan-ignore-next-line */
        self::assertIsString($microdata);
        self::assertStringContainsString('itemscope', $microdata);
        self::assertStringContainsString('itemtype="https://schema.org/Thing"', $microdata);
        self::assertStringContainsString('itemprop="name"', $microdata);
        self::assertStringContainsString('Test Thing', $microdata);
    }

    public function testItShouldConvertToRdfa(): void
    {
        $thing = new Thing(['name' => 'Test Thing']);

        $rdfa = $thing->toRdfa();

        /** @phpstan-ignore-next-line */
        self::assertIsString($rdfa);
        self::assertStringContainsString('vocab="https://schema.org/"', $rdfa);
        self::assertStringContainsString('typeof="Thing"', $rdfa);
        self::assertStringContainsString('property="name"', $rdfa);
        self::assertStringContainsString('Test Thing', $rdfa);
    }

    public function testItShouldProvideValidPropertiesList(): void
    {
        $validProperties = Thing::getValidProperties();

        /** @phpstan-ignore-next-line */
        self::assertIsArray($validProperties);
        self::assertContains('name', $validProperties);
        self::assertContains('description', $validProperties);
        self::assertContains('url', $validProperties);
        self::assertContains('identifier', $validProperties);
        self::assertContains('image', $validProperties);
        self::assertContains('sameAs', $validProperties);
        self::assertContains('alternateName', $validProperties);
    }

    public function testItShouldHaveNoRequiredProperties(): void
    {
        $requiredProperties = Thing::getRequiredProperties();

        self::assertSame([], $requiredProperties);
    }

    /**
     * @dataProvider provideIt_should_convert_datetime_to_iso_format_in_arrayCases
     */
    public function testItShouldConvertDatetimeToIsoFormatInArray(DateTimeInterface $dateTime, string $expected): void
    {
        $thing = new Thing(['dateCreated' => $dateTime]);

        $array = $thing->toArray();

        self::assertSame($expected, $array['dateCreated']);
    }

    /**
     * @return iterable<string, array{DateTime|DateTimeImmutable, string}>
     */
    public static function provideIt_should_convert_datetime_to_iso_format_in_arrayCases(): iterable
    {
        yield 'DateTime' => [
            new DateTime('2024-01-15T10:30:00+00:00'),
            '2024-01-15T10:30:00+00:00',
        ];

        yield 'DateTimeImmutable' => [
            new DateTimeImmutable('2024-01-15T10:30:00+00:00'),
            '2024-01-15T10:30:00+00:00',
        ];
    }
}
