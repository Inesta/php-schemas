<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Validation\Rules;

use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Thing;
use Inesta\Schemas\Validation\Rules\RequiredPropertiesRule;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Validation\Rules\RequiredPropertiesRule
 *
 * @internal
 */
final class RequiredPropertiesRuleTest extends TestCase
{
    private RequiredPropertiesRule $rule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new RequiredPropertiesRule();
    }

    public function testGetRuleId(): void
    {
        self::assertSame('required_properties', $this->rule->getRuleId());
    }

    public function testGetDescription(): void
    {
        $description = $this->rule->getDescription();
        self::assertNotEmpty($description);
    }

    public function testGetSeverity(): void
    {
        self::assertSame('error', $this->rule->getSeverity());
    }

    public function testAppliesToAbstractSchemaTypes(): void
    {
        $thing = new Thing(['name' => 'Test']);
        $article = new Article(['headline' => 'Test']);

        self::assertTrue($this->rule->appliesTo($thing));
        self::assertTrue($this->rule->appliesTo($article));
    }

    public function testValidatesThingWithoutRequiredProperties(): void
    {
        $thing = new Thing(['name' => 'Test Thing']);
        $result = $this->rule->validate($thing);

        self::assertTrue($result->isValid());
        self::assertEmpty($result->getErrors());
    }

    public function testValidatesArticleWithRequiredHeadline(): void
    {
        $article = new Article(['headline' => 'Test Article']);
        $result = $this->rule->validate($article);

        self::assertTrue($result->isValid());
        self::assertEmpty($result->getErrors());
    }

    public function testDetectsMissingRequiredHeadline(): void
    {
        $article = new Article(['description' => 'Test without headline']);
        $result = $this->rule->validate($article);

        self::assertFalse($result->isValid());
        self::assertCount(1, $result->getErrors());

        $error = $result->getErrors()[0];
        self::assertSame('REQUIRED_PROPERTY_MISSING', $error->getCode());
        self::assertSame('headline', $error->getProperty());
        self::assertStringContainsString('headline', $error->getMessage());
    }

    public function testValidatesArticleWithAllRequiredProperties(): void
    {
        $article = new Article([
            'headline' => 'Complete Article',
            'description' => 'A complete article with all required properties',
        ]);

        $result = $this->rule->validate($article);

        self::assertTrue($result->isValid());
        self::assertEmpty($result->getErrors());
    }
}
