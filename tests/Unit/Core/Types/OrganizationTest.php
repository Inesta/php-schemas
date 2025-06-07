<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Types;

use DateTimeImmutable;
use Inesta\Schemas\Core\Types\Organization;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * @covers \Inesta\Schemas\Core\Types\Organization
 *
 * @internal
 */
final class OrganizationTest extends TestCase
{
    public function testCanCreateOrganizationWithMinimalProperties(): void
    {
        $organization = new Organization([
            'name' => 'Example Corp',
        ]);

        self::assertSame('Organization', $organization->getType());
        self::assertSame('Example Corp', $organization->getProperty('name'));
        self::assertSame('https://schema.org', $organization->getContext());
    }

    public function testCanCreateOrganizationWithComprehensiveProperties(): void
    {
        $foundingDate = new DateTimeImmutable('2000-01-01');

        $organization = new Organization([
            'name' => 'Acme Corporation',
            'legalName' => 'Acme Corporation Inc.',
            'description' => 'A leading provider of innovative solutions',
            'url' => 'https://acme.com',
            'logo' => 'https://acme.com/logo.png',
            'email' => 'contact@acme.com',
            'telephone' => '+1-800-ACME',
            'foundingDate' => $foundingDate,
            'foundingLocation' => 'San Francisco, CA',
            'numberOfEmployees' => 500,
            'taxID' => '12-3456789',
            'duns' => '123456789',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '123 Business St',
                'addressLocality' => 'San Francisco',
                'addressRegion' => 'CA',
                'postalCode' => '94105',
                'addressCountry' => 'US',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+1-800-SUPPORT',
                'contactType' => 'customer service',
                'email' => 'support@acme.com',
            ],
            'areaServed' => 'Global',
            'slogan' => 'Innovation at its finest',
            'keywords' => ['technology', 'innovation', 'solutions'],
        ]);

        self::assertSame('Organization', $organization->getType());
        self::assertSame('Acme Corporation', $organization->getProperty('name'));
        self::assertSame('Acme Corporation Inc.', $organization->getProperty('legalName'));
        self::assertSame('A leading provider of innovative solutions', $organization->getProperty('description'));
        self::assertSame('https://acme.com', $organization->getProperty('url'));
        self::assertSame('https://acme.com/logo.png', $organization->getProperty('logo'));
        self::assertSame('contact@acme.com', $organization->getProperty('email'));
        self::assertSame('+1-800-ACME', $organization->getProperty('telephone'));
        self::assertSame($foundingDate, $organization->getProperty('foundingDate'));
        self::assertSame('San Francisco, CA', $organization->getProperty('foundingLocation'));
        self::assertSame(500, $organization->getProperty('numberOfEmployees'));
        self::assertSame('12-3456789', $organization->getProperty('taxID'));
        self::assertSame('123456789', $organization->getProperty('duns'));
        self::assertSame('Global', $organization->getProperty('areaServed'));
        self::assertSame('Innovation at its finest', $organization->getProperty('slogan'));
        self::assertSame(['technology', 'innovation', 'solutions'], $organization->getProperty('keywords'));
    }

    public function testCanCreateOrganizationWithCustomContext(): void
    {
        $organization = new Organization(
            ['name' => 'Test Organization'],
            'https://custom.context.com',
        );

        self::assertSame('https://custom.context.com', $organization->getContext());
    }

    public function testImmutabilityWithPropertyAddition(): void
    {
        $original = new Organization(['name' => 'Original Corp']);
        $modified = $original->withProperty('email', 'contact@original.com');

        self::assertNotSame($original, $modified);
        self::assertNull($original->getProperty('email'));
        self::assertSame('contact@original.com', $modified->getProperty('email'));
        self::assertSame('Original Corp', $original->getProperty('name'));
        self::assertSame('Original Corp', $modified->getProperty('name'));
    }

    public function testImmutabilityWithPropertyModification(): void
    {
        $original = new Organization([
            'name' => 'Original Corp',
            'email' => 'original@example.com',
        ]);

        $modified = $original->withProperty('name', 'Modified Corp');

        self::assertNotSame($original, $modified);
        self::assertSame('Original Corp', $original->getProperty('name'));
        self::assertSame('Modified Corp', $modified->getProperty('name'));
        self::assertSame('original@example.com', $original->getProperty('email'));
        self::assertSame('original@example.com', $modified->getProperty('email'));
    }

    public function testHasProperty(): void
    {
        $organization = new Organization([
            'name' => 'Test Corp',
            'email' => 'test@example.com',
        ]);

        self::assertTrue($organization->hasProperty('name'));
        self::assertTrue($organization->hasProperty('email'));
        self::assertFalse($organization->hasProperty('nonexistent'));
    }

    public function testToArrayIncludesContextAndType(): void
    {
        $organization = new Organization([
            'name' => 'Test Corp',
            'email' => 'test@example.com',
        ]);

        $array = $organization->toArray();

        self::assertArrayHasKey('@context', $array);
        self::assertArrayHasKey('@type', $array);
        self::assertSame('https://schema.org', $array['@context']);
        self::assertSame('Organization', $array['@type']);
        self::assertSame('Test Corp', $array['name']);
        self::assertSame('test@example.com', $array['email']);
    }

    public function testToArrayWithNestedSchemaObject(): void
    {
        $parentOrg = new Thing(['name' => 'Parent Corporation']);
        $organization = new Organization([
            'name' => 'Subsidiary Corp',
            'parentOrganization' => $parentOrg,
        ]);

        $array = $organization->toArray();

        self::assertIsArray($array['parentOrganization']);
        self::assertSame('Thing', $array['parentOrganization']['@type']);
        self::assertSame('Parent Corporation', $array['parentOrganization']['name']);
    }

    public function testToArrayWithDateTime(): void
    {
        $foundingDate = new DateTimeImmutable('2000-01-01T00:00:00+00:00');
        $organization = new Organization([
            'name' => 'Test Corp',
            'foundingDate' => $foundingDate,
        ]);

        $array = $organization->toArray();

        self::assertSame('2000-01-01T00:00:00+00:00', $array['foundingDate']);
    }

    public function testToArrayWithNestedArray(): void
    {
        $department1 = new Thing(['name' => 'Engineering']);
        $department2 = new Thing(['name' => 'Marketing']);

        $organization = new Organization([
            'name' => 'Test Corp',
            'department' => [$department1, $department2],
        ]);

        $array = $organization->toArray();

        self::assertIsArray($array['department']);
        self::assertCount(2, $array['department']);
        self::assertSame('Thing', $array['department'][0]['@type']);
        self::assertSame('Engineering', $array['department'][0]['name']);
        self::assertSame('Thing', $array['department'][1]['@type']);
        self::assertSame('Marketing', $array['department'][1]['name']);
    }

    public function testGetSchemaTypeReturnsCorrectType(): void
    {
        self::assertSame('Organization', Organization::getSchemaType());
    }

    public function testGetRequiredPropertiesIsEmpty(): void
    {
        $required = Organization::getRequiredProperties();

        self::assertEmpty($required);
    }

    public function testGetOptionalPropertiesIncludesCommonProperties(): void
    {
        $optional = Organization::getOptionalProperties();

        self::assertContains('name', $optional);
        self::assertContains('legalName', $optional);
        self::assertContains('description', $optional);
        self::assertContains('url', $optional);
        self::assertContains('logo', $optional);
        self::assertContains('email', $optional);
        self::assertContains('telephone', $optional);
        self::assertContains('foundingDate', $optional);
        self::assertContains('foundingLocation', $optional);
        self::assertContains('numberOfEmployees', $optional);
        self::assertContains('taxID', $optional);
        self::assertContains('address', $optional);
        self::assertContains('contactPoint', $optional);
        self::assertContains('areaServed', $optional);
        self::assertContains('slogan', $optional);
        self::assertContains('keywords', $optional);
    }

    public function testGetValidPropertiesIncludesBothRequiredAndOptional(): void
    {
        $valid = Organization::getValidProperties();
        $required = Organization::getRequiredProperties();
        $optional = Organization::getOptionalProperties();

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
        $organization = new Organization([
            'name' => 'Test Corp',
            'email' => 'test@example.com',
        ]);

        $jsonLd = $organization->toJsonLd();
        $microdata = $organization->toMicrodata();
        $rdfa = $organization->toRdfa();

        self::assertNotEmpty($jsonLd);
        self::assertNotEmpty($microdata);
        self::assertNotEmpty($rdfa);
    }
}
