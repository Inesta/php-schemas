# Contributing to PHP Schema.org Library

Thank you for your interest in contributing to the PHP Schema.org Library! This document outlines the process and guidelines for contributing to this project.

## Code of Conduct

By participating in this project, you agree to abide by our Code of Conduct. Please treat all community members with respect and create a welcoming environment for everyone.

## Development Setup

### Prerequisites

- PHP 8.3 or higher
- Composer
- Git

### Installation

1. Fork the repository on GitHub
2. Clone your fork locally:
   ```bash
   git clone https://github.com/your-username/php-schemas.git
   cd php-schemas
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Set up git hooks:
   ```bash
   vendor/bin/captainhook install
   ```

## Development Workflow

### Before You Start

1. Check the [issue tracker](https://github.com/inesta/php-schemas/issues) for existing issues
2. If you're adding a new feature, please open an issue first to discuss it
3. Fork the repository and create a feature branch from `main`

### Making Changes

1. **Create a branch** for your feature or bug fix:
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-bug-fix
   ```

2. **Follow coding standards** - all code must pass our quality checks:
   ```bash
   # Check code style
   make cs-check
   
   # Fix code style issues
   make cs-fix
   
   # Run static analysis
   make analyse
   make psalm
   
   # Run all checks
   make check-all
   ```

3. **Write tests first (TDD)**:
   - Write failing tests for your feature/fix
   - Implement the minimum code to make tests pass
   - Refactor while keeping tests green

4. **Ensure test coverage**:
   ```bash
   # Run tests
   make test
   
   # Generate coverage report
   make coverage
   ```
   - Minimum 95% code coverage required
   - All tests must pass

## Coding Standards

### PHP Standards

- **PHP 8.3+** features must be used where appropriate
- **Strict types** declaration required in every file
- **PSR-12** coding standard enforced
- **PHPStan Level 9** compliance required
- **Psalm Level 1** compliance required

### Code Structure

```php
<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Types;

use Inesta\Schemas\Core\AbstractSchemaType;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Represents a Schema.org Article type.
 *
 * @see https://schema.org/Article
 */
final class Article extends AbstractSchemaType
{
    public function __construct(
        private readonly string $headline,
        private readonly ?Person $author = null,
    ) {
        parent::__construct();
    }

    public static function getSchemaType(): string
    {
        return 'Article';
    }

    public function validate(): ValidationResult
    {
        // Implementation
    }
}
```

### Testing Standards

- **Test-Driven Development** required
- Use descriptive test method names: `it_should_validate_required_properties`
- Use data providers for multiple test scenarios
- Test edge cases and error conditions
- Mock external dependencies

Example test:

```php
<?php

declare(strict_types=1);

namespace Inesta\Schemas\Tests\Unit\Core\Types;

use Inesta\Schemas\Core\Types\Article;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inesta\Schemas\Core\Types\Article
 */
final class ArticleTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_create_article_with_required_properties(): void
    {
        $article = new Article('Test Headline');
        
        self::assertSame('Article', $article->getSchemaType());
        self::assertSame('Test Headline', $article->getHeadline());
    }
}
```

## Testing

### Running Tests

```bash
# Run all tests
make test

# Run specific test suites
make test-unit
make test-integration
make test-compliance

# Run with coverage
make coverage

# Run mutation testing
make infection
```

### Test Structure

- `tests/Unit/` - Unit tests for individual classes
- `tests/Integration/` - Integration tests for workflows
- `tests/Compliance/` - Schema.org compliance tests

### Adding New Tests

1. Create test files in the appropriate directory
2. Follow the naming convention: `ClassNameTest.php`
3. Use the `@covers` annotation to specify what you're testing
4. Write descriptive test names that explain the behavior

## Documentation

### API Documentation

- All public methods must have PHPDoc blocks
- Include `@param`, `@return`, and `@throws` annotations
- Document any side effects or special behavior

### Examples

When adding new schema types, include examples in the appropriate documentation directory:

- `docs/json-ld/examples/`
- `docs/microdata/examples/`
- `docs/rdfa/examples/`

## Validation and Schema Compliance

All schemas must be validated against:

1. **Schema.org Validator**: https://validator.schema.org/
2. **Google Rich Results Test**
3. **W3C Validators** for HTML output

Use the validation commands:

```bash
make validate-schema
make validate-examples
```

## Submitting Changes

### Pull Request Process

1. **Ensure all checks pass**:
   ```bash
   make check-all
   ```

2. **Update documentation** if needed

3. **Add or update tests** for your changes

4. **Update CHANGELOG.md** following [Keep a Changelog](https://keepachangelog.com/) format

5. **Create a pull request** with:
   - Clear title following conventional commits format
   - Detailed description of changes
   - Link to related issues
   - Screenshots/examples if relevant

### Commit Message Format

Follow [Conventional Commits](https://conventionalcommits.org/):

```
type(scope): description

[optional body]

[optional footer]
```

Examples:
- `feat(core): add Article schema type`
- `fix(validation): handle empty property values`
- `docs(readme): update installation instructions`
- `test(article): add validation tests`

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks
- `perf`: Performance improvements
- `ci`: CI/CD changes

## Release Process

Releases are handled automatically when tags are pushed:

1. Update `CHANGELOG.md`
2. Create and push a tag: `git tag v1.0.0 && git push origin v1.0.0`
3. GitHub Actions will create the release and update Packagist

## Getting Help

- **Questions**: Open a [Discussion](https://github.com/inesta/php-schemas/discussions)
- **Bug Reports**: Use the [Bug Report template](https://github.com/inesta/php-schemas/issues/new?template=bug_report.yml)
- **Feature Requests**: Use the [Feature Request template](https://github.com/inesta/php-schemas/issues/new?template=feature_request.yml)

## Recognition

Contributors will be recognized in:
- The `README.md` file
- Release notes
- The `CONTRIBUTORS.md` file (if you'd like to be listed)

Thank you for contributing to the PHP Schema.org Library! 🚀