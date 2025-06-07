<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Builder\Builders;

use Inesta\Schemas\Builder\Builders\ThingBuilder;
use Inesta\Schemas\Core\Types\Thing;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Builder\Builders\ThingBuilder
 *
 * @internal
 */
final class ThingBuilderTest extends TestCase
{
    private ThingBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ThingBuilder();
    }

    public function testCanBuildMinimalThing(): void
    {
        $thing = $this->builder
            ->name('Test Thing')
            ->build()
        ;

        self::assertInstanceOf(Thing::class, $thing);
        self::assertSame('Test Thing', $thing->getProperty('name'));
        self::assertSame('https://schema.org', $thing->getContext());
    }

    public function testFluentInterface(): void
    {
        $result = $this->builder->name('Test');

        self::assertSame($this->builder, $result);
    }

    public function testCanBuildComprehensiveThing(): void
    {
        $thing = $this->builder
            ->name('Comprehensive Thing')
            ->description('A comprehensive test thing')
            ->url('https://example.com/thing')
            ->image('https://example.com/image.jpg')
            ->identifier('test-thing-123')
            ->alternateName('Alternative Name')
            ->disambiguatingDescription('Disambiguating description')
            ->mainEntityOfPage('https://example.com/page')
            ->additionalType('https://example.com/type')
            ->sameAs('https://example.com/same1')
            ->sameAs('https://example.com/same2')
            ->subjectOf(['@type' => 'Article', 'name' => 'Article about thing'])
            ->potentialAction(['@type' => 'Action', 'name' => 'View'])
            ->build()
        ;

        self::assertSame('Comprehensive Thing', $thing->getProperty('name'));
        self::assertSame('A comprehensive test thing', $thing->getProperty('description'));
        self::assertSame('https://example.com/thing', $thing->getProperty('url'));
        self::assertSame('https://example.com/image.jpg', $thing->getProperty('image'));
        self::assertSame('test-thing-123', $thing->getProperty('identifier'));
        self::assertSame('Alternative Name', $thing->getProperty('alternateName'));
        self::assertSame('Disambiguating description', $thing->getProperty('disambiguatingDescription'));
        self::assertSame('https://example.com/page', $thing->getProperty('mainEntityOfPage'));
        self::assertSame('https://example.com/type', $thing->getProperty('additionalType'));
        self::assertSame([
            'https://example.com/same1',
            'https://example.com/same2',
        ], $thing->getProperty('sameAs'));
        self::assertSame(['@type' => 'Article', 'name' => 'Article about thing'], $thing->getProperty('subjectOf'));
        self::assertSame([['@type' => 'Action', 'name' => 'View']], $thing->getProperty('potentialAction'));
    }

    public function testCanSetCustomContext(): void
    {
        $thing = $this->builder
            ->setContext('https://custom.context')
            ->name('Test Thing')
            ->build()
        ;

        self::assertSame('https://custom.context', $thing->getContext());
    }

    public function testReset(): void
    {
        $this->builder
            ->name('Test Thing')
            ->description('Test Description')
            ->setContext('https://custom.context')
        ;

        $this->builder->reset();

        self::assertSame([], $this->builder->getData());
        self::assertSame('https://schema.org', $this->builder->getContext());
    }

    public function testMultiplePotentialActions(): void
    {
        $thing = $this->builder
            ->name('Test Thing')
            ->potentialAction(['@type' => 'ViewAction', 'name' => 'View'])
            ->potentialAction(['@type' => 'ShareAction', 'name' => 'Share'])
            ->build()
        ;

        $potentialActions = $thing->getProperty('potentialAction');
        self::assertIsArray($potentialActions);
        self::assertCount(2, $potentialActions);
        self::assertSame('ViewAction', $potentialActions[0]['@type']);
        self::assertSame('ShareAction', $potentialActions[1]['@type']);
    }

    public function testMultipleSameAsUrls(): void
    {
        $thing = $this->builder
            ->name('Test Thing')
            ->sameAs('https://wikidata.org/entity/Q123')
            ->sameAs('https://dbpedia.org/resource/Test_Thing')
            ->sameAs('https://freebase.com/m/abc123')
            ->build()
        ;

        $sameAs = $thing->getProperty('sameAs');
        self::assertIsArray($sameAs);
        self::assertCount(3, $sameAs);
        self::assertContains('https://wikidata.org/entity/Q123', $sameAs);
        self::assertContains('https://dbpedia.org/resource/Test_Thing', $sameAs);
        self::assertContains('https://freebase.com/m/abc123', $sameAs);
    }
}
