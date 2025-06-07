<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Types;

use DateTimeImmutable;
use Inesta\Schemas\Core\Types\Person;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * @covers \Inesta\Schemas\Core\Types\Person
 *
 * @internal
 */
final class PersonTest extends TestCase
{
    public function testCanCreatePersonWithMinimalProperties(): void
    {
        $person = new Person([
            'name' => 'John Doe',
        ]);

        self::assertSame('Person', $person->getType());
        self::assertSame('John Doe', $person->getProperty('name'));
        self::assertSame('https://schema.org', $person->getContext());
    }

    public function testCanCreatePersonWithComprehensiveProperties(): void
    {
        $birthDate = new DateTimeImmutable('1990-01-01');

        $person = new Person([
            'name' => 'Jane Smith',
            'givenName' => 'Jane',
            'familyName' => 'Smith',
            'email' => 'jane.smith@example.com',
            'telephone' => '+1234567890',
            'jobTitle' => 'Software Engineer',
            'birthDate' => $birthDate,
            'gender' => 'Female',
            'nationality' => 'American',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '123 Main St',
                'addressLocality' => 'Anytown',
                'addressRegion' => 'CA',
                'postalCode' => '12345',
                'addressCountry' => 'US',
            ],
            'worksFor' => [
                '@type' => 'Organization',
                'name' => 'Example Corp',
            ],
            'url' => 'https://janesmith.example.com',
            'image' => 'https://example.com/jane-photo.jpg',
            'description' => 'Experienced software engineer specializing in web development',
        ]);

        self::assertSame('Person', $person->getType());
        self::assertSame('Jane Smith', $person->getProperty('name'));
        self::assertSame('Jane', $person->getProperty('givenName'));
        self::assertSame('Smith', $person->getProperty('familyName'));
        self::assertSame('jane.smith@example.com', $person->getProperty('email'));
        self::assertSame('+1234567890', $person->getProperty('telephone'));
        self::assertSame('Software Engineer', $person->getProperty('jobTitle'));
        self::assertSame($birthDate, $person->getProperty('birthDate'));
        self::assertSame('Female', $person->getProperty('gender'));
        self::assertSame('American', $person->getProperty('nationality'));
        self::assertSame('https://janesmith.example.com', $person->getProperty('url'));
        self::assertSame('https://example.com/jane-photo.jpg', $person->getProperty('image'));
        self::assertSame('Experienced software engineer specializing in web development', $person->getProperty('description'));
    }

    public function testCanCreatePersonWithCustomContext(): void
    {
        $person = new Person(
            ['name' => 'Test Person'],
            'https://custom.context.com',
        );

        self::assertSame('https://custom.context.com', $person->getContext());
    }

    public function testImmutabilityWithPropertyAddition(): void
    {
        $original = new Person(['name' => 'Original Name']);
        $modified = $original->withProperty('email', 'test@example.com');

        self::assertNotSame($original, $modified);
        self::assertNull($original->getProperty('email'));
        self::assertSame('test@example.com', $modified->getProperty('email'));
        self::assertSame('Original Name', $original->getProperty('name'));
        self::assertSame('Original Name', $modified->getProperty('name'));
    }

    public function testImmutabilityWithPropertyModification(): void
    {
        $original = new Person([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $modified = $original->withProperty('name', 'Modified Name');

        self::assertNotSame($original, $modified);
        self::assertSame('Original Name', $original->getProperty('name'));
        self::assertSame('Modified Name', $modified->getProperty('name'));
        self::assertSame('original@example.com', $original->getProperty('email'));
        self::assertSame('original@example.com', $modified->getProperty('email'));
    }

    public function testHasProperty(): void
    {
        $person = new Person([
            'name' => 'Test Person',
            'email' => 'test@example.com',
        ]);

        self::assertTrue($person->hasProperty('name'));
        self::assertTrue($person->hasProperty('email'));
        self::assertFalse($person->hasProperty('nonexistent'));
    }

    public function testToArrayIncludesContextAndType(): void
    {
        $person = new Person([
            'name' => 'Test Person',
            'email' => 'test@example.com',
        ]);

        $array = $person->toArray();

        self::assertArrayHasKey('@context', $array);
        self::assertArrayHasKey('@type', $array);
        self::assertSame('https://schema.org', $array['@context']);
        self::assertSame('Person', $array['@type']);
        self::assertSame('Test Person', $array['name']);
        self::assertSame('test@example.com', $array['email']);
    }

    public function testToArrayWithNestedSchemaObject(): void
    {
        $worksFor = new Thing(['name' => 'Example Company']);
        $person = new Person([
            'name' => 'Test Person',
            'worksFor' => $worksFor,
        ]);

        $array = $person->toArray();

        self::assertIsArray($array['worksFor']);
        self::assertSame('Thing', $array['worksFor']['@type']);
        self::assertSame('Example Company', $array['worksFor']['name']);
    }

    public function testToArrayWithDateTime(): void
    {
        $birthDate = new DateTimeImmutable('1990-01-01T00:00:00+00:00');
        $person = new Person([
            'name' => 'Test Person',
            'birthDate' => $birthDate,
        ]);

        $array = $person->toArray();

        self::assertSame('1990-01-01T00:00:00+00:00', $array['birthDate']);
    }

    public function testToArrayWithNestedArray(): void
    {
        $skill1 = new Thing(['name' => 'PHP']);
        $skill2 = new Thing(['name' => 'JavaScript']);

        $person = new Person([
            'name' => 'Test Person',
            'knowsAbout' => [$skill1, $skill2],
        ]);

        $array = $person->toArray();

        self::assertIsArray($array['knowsAbout']);
        self::assertCount(2, $array['knowsAbout']);
        self::assertSame('Thing', $array['knowsAbout'][0]['@type']);
        self::assertSame('PHP', $array['knowsAbout'][0]['name']);
        self::assertSame('Thing', $array['knowsAbout'][1]['@type']);
        self::assertSame('JavaScript', $array['knowsAbout'][1]['name']);
    }

    public function testGetSchemaTypeReturnsCorrectType(): void
    {
        self::assertSame('Person', Person::getSchemaType());
    }

    public function testGetRequiredPropertiesIsEmpty(): void
    {
        $required = Person::getRequiredProperties();

        self::assertEmpty($required);
    }

    public function testGetOptionalPropertiesIncludesCommonProperties(): void
    {
        $optional = Person::getOptionalProperties();

        self::assertContains('name', $optional);
        self::assertContains('givenName', $optional);
        self::assertContains('familyName', $optional);
        self::assertContains('email', $optional);
        self::assertContains('telephone', $optional);
        self::assertContains('jobTitle', $optional);
        self::assertContains('birthDate', $optional);
        self::assertContains('gender', $optional);
        self::assertContains('nationality', $optional);
        self::assertContains('address', $optional);
        self::assertContains('worksFor', $optional);
        self::assertContains('description', $optional);
        self::assertContains('image', $optional);
        self::assertContains('url', $optional);
    }

    public function testGetValidPropertiesIncludesBothRequiredAndOptional(): void
    {
        $valid = Person::getValidProperties();
        $required = Person::getRequiredProperties();
        $optional = Person::getOptionalProperties();

        self::assertCount(count($required) + count($optional), $valid);

        foreach ($required as $property) {
            self::assertContains($property, $valid);
        }

        foreach ($optional as $property) {
            self::assertContains($property, $valid);
        }
    }

    public function testRendererMethods(): void
    {
        $person = new Person([
            'name' => 'Test Person',
            'email' => 'test@example.com',
        ]);

        $jsonLd = $person->toJsonLd();
        $microdata = $person->toMicrodata();
        $rdfa = $person->toRdfa();

        self::assertNotEmpty($jsonLd);
        self::assertNotEmpty($microdata);
        self::assertNotEmpty($rdfa);
    }
}
