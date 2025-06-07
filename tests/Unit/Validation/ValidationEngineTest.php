<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Validation;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Thing;
use Inesta\Schemas\Validation\Interfaces\ValidationRuleInterface;
use Inesta\Schemas\Validation\ValidationEngine;
use Inesta\Schemas\Validation\ValidationError;
use Inesta\Schemas\Validation\ValidationResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Validation\ValidationEngine
 *
 * @internal
 */
final class ValidationEngineTest extends TestCase
{
    private ValidationEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new ValidationEngine();
    }

    public function testHasDefaultRules(): void
    {
        $supportedRules = $this->engine->getSupportedRules();

        self::assertContains('required_properties', $supportedRules);
        self::assertContains('property_types', $supportedRules);
        self::assertContains('empty_values', $supportedRules);
        self::assertContains('schema_org_compliance', $supportedRules);
    }

    public function testCanAddCustomRule(): void
    {
        $customRule = $this->createMockRule('custom_rule');
        $this->engine->addRule($customRule);

        self::assertTrue($this->engine->hasRule('custom_rule'));
        self::assertSame($customRule, $this->engine->getRule('custom_rule'));
    }

    public function testCanRemoveRule(): void
    {
        $this->engine->removeRule('empty_values');

        self::assertFalse($this->engine->hasRule('empty_values'));
        self::assertNull($this->engine->getRule('empty_values'));
    }

    public function testCanClearAllRules(): void
    {
        $this->engine->clearRules();

        self::assertEmpty($this->engine->getRules());
        self::assertEmpty($this->engine->getSupportedRules());
    }

    public function testValidatesValidSchema(): void
    {
        $schema = new Thing(['name' => 'Valid Thing']);
        $result = $this->engine->validate($schema);

        self::assertTrue($result->isValid());
        self::assertEmpty($result->getErrors());
    }

    public function testDetectsRequiredPropertyErrors(): void
    {
        $schema = new Article([]); // Missing required 'headline'
        $result = $this->engine->validate($schema);

        self::assertFalse($result->isValid());
        self::assertGreaterThan(0, $result->getErrorCount());

        $errorMessages = $result->getErrorMessages();
        self::assertContains("Required property 'headline' is missing", $errorMessages);
    }

    public function testDetectsEmptyValueWarnings(): void
    {
        $schema = new Thing(['name' => '']);
        $result = $this->engine->validate($schema);

        self::assertTrue($result->isValid()); // Empty values are warnings, not errors
        self::assertGreaterThan(0, $result->getWarningCount());
    }

    public function testGetApplicableRules(): void
    {
        $schema = new Thing(['name' => 'Test']);
        $applicableRules = $this->engine->getApplicableRules($schema);

        self::assertNotEmpty($applicableRules);

        foreach ($applicableRules as $rule) {
            self::assertTrue($rule->appliesTo($schema));
        }
    }

    public function testStopOnFirstError(): void
    {
        // Create a schema with multiple errors
        $schema = new Article(['invalid_url' => 'not-a-url']); // Missing headline + invalid URL

        // Test with stop on first error disabled (default)
        $this->engine->setStopOnFirstError(false);
        $result1 = $this->engine->validate($schema);

        // Test with stop on first error enabled
        $this->engine->setStopOnFirstError(true);
        $result2 = $this->engine->validate($schema);

        // Should have same or more errors when not stopping on first
        self::assertGreaterThanOrEqual($result2->getErrorCount(), $result1->getErrorCount());
    }

    public function testCustomRuleIntegration(): void
    {
        $customRule = $this->createMockRule('always_error', 'error');
        $this->engine->addRule($customRule);

        $schema = new Thing(['name' => 'Test']);
        $result = $this->engine->validate($schema);

        self::assertFalse($result->isValid());

        $errorMessages = $result->getErrorMessages();
        self::assertContains('Custom error message', $errorMessages);
    }

    public function testCustomWarningRule(): void
    {
        $customRule = $this->createMockRule('always_warning', 'warning');
        $this->engine->addRule($customRule);

        $schema = new Thing(['name' => 'Test']);
        $result = $this->engine->validate($schema);

        self::assertTrue($result->isValid()); // Warnings don't affect validity
        self::assertGreaterThan(0, $result->getWarningCount());
    }

    /**
     * Create a mock validation rule for testing.
     *
     * @param string $ruleId   The rule identifier
     * @param string $severity The rule severity
     *
     * @return ValidationRuleInterface The mock rule
     */
    private function createMockRule(string $ruleId, string $severity = 'error'): ValidationRuleInterface
    {
        return new class($ruleId, $severity) implements ValidationRuleInterface {
            public function __construct(
                private string $ruleId,
                private string $severity,
            ) {}

            public function getRuleId(): string
            {
                return $this->ruleId;
            }

            public function getDescription(): string
            {
                return "Mock rule: {$this->ruleId}";
            }

            public function appliesTo(SchemaTypeInterface $schema): bool
            {
                return true;
            }

            public function validate(SchemaTypeInterface $schema): ValidationResult
            {
                $error = new ValidationError(
                    'Custom error message',
                    'CUSTOM_ERROR',
                    'test_property',
                    'test_value',
                );

                return new ValidationResult([$error]);
            }

            public function getSeverity(): string
            {
                return $this->severity;
            }
        };
    }
}
