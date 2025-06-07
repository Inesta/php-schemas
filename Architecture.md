# Architecture Overview

## Core Design Principles

### 1. Type Safety First
The library leverages PHP 8.3's advanced type system to provide compile-time safety and IDE support. Every Schema.org type is represented as a PHP class with strongly-typed properties using:
- Union types for flexible property values
- Intersection types where applicable
- Never and null standalone types
- Readonly properties for immutability
- Enums for predefined value sets

### 2. Immutability
Schema objects are immutable by default using readonly properties, ensuring thread safety and predictable behavior. Each modification returns a new instance.

### 3. Fluent Interface
Builder pattern implementation allows for intuitive, chainable method calls that mirror natural language, with full type inference support.

### 4. Separation of Concerns
Clear separation between schema definition, validation, and rendering layers with dependency injection.

### 5. Quality Enforcement
**MANDATORY: All code must meet these standards before acceptance:**
- PHP 8.3+ features utilized fully
- PHPStan Level 9 compliance
- Psalm Level 1 compliance
- 95%+ test coverage
- Zero code quality issues

## Component Architecture

### Core Layer

#### Schema Registry
- Central repository of all Schema.org type definitions
- Manages type hierarchy and inheritance
- Provides type discovery and validation rules

#### Type System
```
SchemaType (Abstract)
├── Thing
│   ├── CreativeWork
│   │   ├── Article
│   │   ├── Book
│   │   └── ...
│   ├── Event
│   ├── Organization
│   ├── Person
│   └── ...
└── DataType
    ├── Text
    ├── Number
    ├── Date
    └── ...
```

#### Property Manager
- Handles property definitions and constraints
- Manages property inheritance
- Validates property values against Schema.org specifications

### Builder Layer

#### Factory System
```php
namespace Schema\Factory;

class SchemaFactory {
    public function createType(string $type): SchemaTypeInterface
    public function __call(string $method, array $args): SchemaTypeInterface
}
```

#### Builder Classes
Each schema type has a corresponding builder:
- `ArticleBuilder`
- `PersonBuilder`
- `OrganizationBuilder`
- etc.

### Validation Layer

#### Validator Engine
- Rule-based validation system
- Extensible validator architecture
- Support for custom validation rules

#### Validation Rules
- Required properties check
- Type compatibility validation
- Format validation (URLs, emails, dates)
- Cardinality constraints
- Domain-specific rules

### Rendering Layer

#### Renderer Interface
```php
interface RendererInterface {
    public function render(SchemaTypeInterface $schema): string;
}
```

#### Concrete Renderers
- `JsonLdRenderer`: Outputs JSON-LD format
- `MicrodataRenderer`: Outputs HTML with Microdata
- `RdfaRenderer`: Outputs HTML with RDFa
- `DebugRenderer`: Human-readable format for debugging

### Extension System

#### Plugin Architecture
- Hook system for extending functionality
- Custom type registration
- Property extension mechanism
- Renderer plugins

#### Framework Adapters
```
Adapters/
├── Laravel/
│   ├── ServiceProvider
│   ├── Facades
│   └── Blade Components
├── Symfony/
│   ├── Bundle
│   └── Twig Extensions
└── WordPress/
    ├── Plugin
    └── Shortcodes
```

## Data Flow

```
User Input
    ↓
Schema Factory
    ↓
Type Builder
    ↓
Property Assignment
    ↓
Validation Engine
    ↓
Schema Object (Immutable)
    ↓
Renderer Selection
    ↓
Output (JSON-LD/Microdata/RDFa)
```

## Key Classes and Interfaces

### Core Interfaces
```php
interface SchemaTypeInterface {
    public function getType(): string;
    public function getProperties(): array;
    public function validate(): ValidationResult;
}

interface BuilderInterface {
    public function build(): SchemaTypeInterface;
}

interface ValidatorInterface {
    public function validate(SchemaTypeInterface $schema): ValidationResult;
}
```

### Base Classes (PHP 8.3+)
```php
<?php

declare(strict_types=1);

namespace Schema\Core;

abstract class AbstractSchemaType implements SchemaTypeInterface 
{
    protected readonly array $properties;
    protected readonly string $type;
    protected readonly string $context = 'https://schema.org';
    
    public function __construct(array $properties = []) 
    {
        $this->properties = $properties;
        $this->type = static::getSchemaType();
    }
    
    abstract public static function getSchemaType(): string;
}

abstract class AbstractBuilder implements BuilderInterface 
{
    protected array $data = [];
    
    public function __call(string $method, array $args): static 
    {
        // Fluent property setting with type checking
        return $this->withProperty($method, $args[0] ?? null);
    }
    
    protected function withProperty(string $name, mixed $value): static 
    {
        $clone = clone $this;
        $clone->data[$name] = $value;
        return $clone;
    }
}
```

## Performance Considerations

### Lazy Loading
- Schema definitions loaded on-demand
- Property metadata cached after first use
- Renderer templates compiled and cached

### Memory Optimization
- Flyweight pattern for common schema instances
- Property value interning for repeated strings
- Efficient JSON serialization

### Caching Strategy
- Schema definition caching
- Rendered output caching
- Validation result caching

## Security Considerations

### Input Sanitization
- All user input sanitized before processing
- XSS prevention in HTML renderers
- JSON injection prevention

### Validation
- Strict type checking
- URL validation for external references
- Prevention of circular references

## Testing Architecture

**MANDATORY: Test-Driven Development (TDD) is required. Tests must be written BEFORE implementation.**

### Testing Standards
- **Minimum 95% code coverage** (enforced, build fails below this)
- **All tests must pass** (no skipped or incomplete tests allowed)
- **PHPUnit 10+** with strict configuration
- **Mutation testing** with Infection PHP (minimum 80% MSI)
- **Performance tests** for all renderers

### Unit Tests
- Individual component testing with PHPUnit
- Strict mocking with Prophecy or PHPUnit mocks
- Data providers for comprehensive input testing
- Test doubles for all external dependencies
- Assertions on exception types and messages

### Integration Tests
- End-to-end schema creation flows
- Renderer output validation against real validators
- Framework adapter testing with real framework instances
- Database integration tests with transactions
- API integration tests with mocked HTTP clients

### Compliance Tests
- Automated Schema.org specification compliance
- Google Rich Results Test API integration
- W3C validator compliance checks
- Cross-format consistency validation
- Performance benchmarks (must not degrade)

### Testing Best Practices
```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Types;

use PHPUnit\Framework\TestCase;
use Schema\Core\Types\Article;

final class ArticleTest extends TestCase
{
    /**
     * @test
     * @dataProvider validArticleDataProvider
     */
    public function it_creates_valid_article_schema(array $data, array $expected): void
    {
        // Arrange
        $article = new Article($data);
        
        // Act
        $result = $article->toArray();
        
        // Assert
        self::assertSame($expected, $result);
    }
    
    public static function validArticleDataProvider(): iterable
    {
        yield 'basic article' => [
            'data' => ['headline' => 'Test Article'],
            'expected' => [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => 'Test Article'
            ]
        ];
        
        // More test cases...
    }
}
```

## Directory Structure

```
src/
├── Core/
│   ├── Types/
│   ├── Properties/
│   ├── Registry/
│   └── Exceptions/
├── Builder/
│   ├── Factory/
│   ├── Builders/
│   └── Interfaces/
├── Validation/
│   ├── Validators/
│   ├── Rules/
│   └── Results/
├── Renderer/
│   ├── JsonLd/
│   ├── Microdata/
│   ├── Rdfa/
│   └── Interfaces/
├── Extension/
│   ├── Plugins/
│   └── Hooks/
└── Adapters/
    ├── Laravel/
    ├── Symfony/
    └── WordPress/

tests/
├── Unit/
├── Integration/
└── Compliance/

resources/
├── schemas/
├── templates/
└── config/
```

## Future Extensibility

### Version Management
- Support for different Schema.org versions
- Backward compatibility layer
- Migration tools for schema updates

### Advanced Features
- Schema composition and merging
- Conditional properties based on context
- Dynamic schema generation from databases
- GraphQL integration for schema queries

### Performance Enhancements
- Ahead-of-time compilation for production
- Native extensions for critical paths
- Streaming renderers for large schemas