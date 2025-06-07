# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a PHP library that provides a fluent, type-safe interface for creating Schema.org structured data. It enables generation of JSON-LD, Microdata, and RDFa markup for web pages to improve SEO and enable rich snippets.

## Key Architecture Principles

1. **Type Safety First** - Every Schema.org type is a PHP class with strongly-typed properties
2. **Immutability** - Schema objects are immutable; modifications return new instances
3. **Fluent Interface** - Builder pattern with chainable methods
4. **Separation of Concerns** - Clear separation between schema definition, validation, and rendering

## Project Structure

```
src/
├── Core/               # Schema type definitions and registry
│   ├── Types/         # Schema.org type classes (Thing, Person, Article, etc.)
│   ├── Properties/    # Property definitions and constraints
│   └── Registry/      # Type hierarchy management
├── Builder/           # Factory and builder classes
│   ├── Factory/      # SchemaFactory for type creation
│   └── Builders/     # Type-specific builders (ArticleBuilder, etc.)
├── Validation/        # Schema validation engine
│   ├── Validators/   # Validation logic
│   └── Rules/        # Validation rule definitions
├── Renderer/          # Output format renderers
│   ├── JsonLd/       # JSON-LD renderer
│   ├── Microdata/    # Microdata renderer
│   └── Rdfa/         # RDFa renderer
└── Adapters/         # Framework integrations
    ├── Laravel/      # Laravel service provider and facades
    └── Symfony/      # Symfony bundle

tests/
├── Unit/             # Unit tests for individual components
├── Integration/      # End-to-end workflow tests
└── Compliance/       # Schema.org specification compliance tests

docs/
├── json-ld/          # JSON-LD examples and documentation
├── microdata/        # Microdata examples and documentation
└── rdfa/             # RDFa examples and documentation
```

## PHP Standards Requirements

**MANDATORY: This project enforces PHP 8.3+ standards. All code MUST pass these checks before implementation.**

### Required PHP Version
- **Minimum PHP 8.3** (latest stable version)
- Use all modern PHP features: typed properties, union types, enums, readonly properties, etc.

### Code Quality Standards
- **PSR-12** coding standard (enforced)
- **PHPStan Level 9** (strictest level)
- **Psalm Level 1** (strictest level)
- **100% type coverage** required
- **Strict types declaration** in every PHP file

## Development Commands

```bash
# Install dependencies
composer install

# MANDATORY: Run before ANY commit
composer check-all     # Runs all checks (standards, static analysis, tests)

# Individual checks (ALL MUST PASS)
composer test          # Run all tests (MUST have 100% pass rate)
composer test:unit     # Unit tests
composer test:integration  # Integration tests
composer test:compliance   # Schema.org compliance tests
composer test:coverage # Code coverage (MUST be >90%)

# Code quality (MUST pass before implementation)
composer cs:check      # Check code standards (PHP_CodeSniffer with PSR-12)
composer cs:fix        # Fix code standards automatically
composer analyse       # Static analysis with PHPStan (Level 9)
composer psalm         # Static analysis with Psalm (Level 1)
composer metrics       # Code metrics and complexity analysis

# Validation
composer validate:schema    # Validate against Schema.org
composer validate:examples  # Validate all examples

# Documentation
composer docs:generate      # Generate API documentation
```

### Pre-commit Hooks (ENFORCED)
```bash
# Automatically installed on composer install
# Runs: cs:check, analyse, psalm, test
# Commit BLOCKED if any check fails
```

## Common Development Tasks

### Creating a New Schema Type
1. Add type class in `src/Core/Types/` with full type hints and strict types
2. Create corresponding builder in `src/Builder/Builders/`
3. Register type in the Schema Registry
4. Add validation rules in `src/Validation/Rules/`
5. Create examples in `docs/{format}/examples/`
6. **MANDATORY: Write tests FIRST (TDD)**
   - Unit tests in `tests/Unit/Types/` (minimum 95% coverage)
   - Integration tests in `tests/Integration/`
   - All tests MUST pass before proceeding
7. **MANDATORY: Run `composer check-all` and fix ALL issues**

### Adding Framework Support
1. Create adapter directory in `src/Adapters/{Framework}/`
2. Implement framework-specific service providers/bundles
3. Add framework examples in documentation
4. Test integration thoroughly

### Testing Schema Validation
- Use Schema.org Validator: https://validator.schema.org/
- Test with Google Rich Results Test for SEO compliance
- Run local validation with EasyRdf for RDFa
- Use structured-data-testing-tool for automated testing

## Code Conventions

**MANDATORY: Every file MUST follow these conventions. NO EXCEPTIONS.**

```php
<?php

declare(strict_types=1);
```

- **PSR-12** coding standards (enforced by PHP_CodeSniffer)
- **Strict types declaration** at the top of EVERY PHP file
- **Full type hints** on all parameters, returns, and properties
- **No mixed types** unless absolutely necessary (document why)
- Implement fluent interfaces returning `self` or new instances
- All public methods must have proper PHPDoc blocks with @throws annotations
- Schema type classes should extend `AbstractSchemaType`
- Builder classes should extend `AbstractBuilder`
- **Final by default** - use `final` keyword on classes unless extension is needed
- **Readonly properties** where applicable (PHP 8.1+)
- **Enums** for any finite set of values (PHP 8.1+)

### Testing Requirements

**MANDATORY: No code is considered complete without tests.**

- **Test-Driven Development (TDD)** is required
- Write tests BEFORE implementation
- Minimum **95% code coverage** (enforced)
- Every public method must have at least one test
- Test edge cases and error conditions
- Use data providers for multiple test scenarios
- Mock external dependencies appropriately

## Important Notes

- Schema.org types use inheritance; respect the type hierarchy
- Properties can have multiple valid types (e.g., author can be Person or Organization)
- Validation should be non-blocking by default but strict when enabled
- Renderers should escape output appropriately for their format
- Always validate generated schemas against official validators

## Performance Considerations

- Use lazy loading for schema definitions
- Cache rendered outputs when possible
- Implement flyweight pattern for common schema instances
- Profile memory usage with large schema collections