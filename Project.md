# PHP Schema.org Library

## Project Overview

This PHP library provides a fluent, type-safe interface for creating and managing Schema.org structured data. It enables developers to easily generate JSON-LD, Microdata, and RDFa markup for web pages, improving SEO and enabling rich snippets in search results.

## Technical Requirements

### PHP Version and Standards
- **Minimum PHP 8.3** (latest stable version required)
- **Strict adherence to modern PHP standards**
- **PSR-12** coding standard (enforced via PHP_CodeSniffer)
- **PHPStan Level 9** and **Psalm Level 1** (strictest static analysis)
- **100% type declarations** on all methods, properties, and parameters
- **Strict types** enabled in every file

### Testing Requirements
- **Test-Driven Development (TDD)** methodology required
- **Minimum 95% code coverage** (enforced in CI/CD)
- **All tests must pass** before any code can be merged
- Comprehensive test suites: unit, integration, and compliance
- Automated testing against Schema.org validators

## Purpose

Schema.org provides a shared vocabulary that webmasters can use to mark up their pages in ways that can be understood by major search engines. This library aims to:

- Simplify the creation of Schema.org structured data in PHP applications
- Provide type safety and validation for schema properties
- Support multiple output formats (JSON-LD, Microdata, RDFa)
- Enable easy integration with popular PHP frameworks
- Reduce errors through IDE autocompletion and type hints

## Key Features

### 1. Fluent API
Create schemas using an intuitive, chainable interface:
```php
$article = Schema::article()
    ->headline('Understanding Web Schemas')
    ->author(Schema::person()->name('John Doe'))
    ->datePublished(new DateTime('2024-01-15'))
    ->publisher(Schema::organization()->name('Tech Blog'));
```

### 2. Type Safety
Leverage PHP's type system to catch errors at development time rather than runtime.

### 3. Validation
Built-in validation ensures schemas comply with Schema.org specifications.

### 4. Multiple Output Formats
Generate structured data in the format that best suits your needs:
- JSON-LD (recommended by Google)
- Microdata
- RDFa

### 5. Extensibility
Easy to extend with custom types and properties while maintaining Schema.org compatibility.

## Target Audience

- Web developers building SEO-optimized websites
- E-commerce platforms needing product markup
- Content management systems requiring structured data
- News and blog platforms
- Event and booking systems
- Any PHP application that benefits from structured data

## Benefits

### For Developers
- Reduced development time through intuitive API
- Fewer bugs with type safety and validation
- Better IDE support with autocompletion
- Comprehensive documentation and examples

### For Businesses
- Improved search engine visibility
- Rich snippets in search results
- Better click-through rates
- Enhanced user experience
- Future-proof structured data implementation

## Use Cases

1. **E-commerce**: Product listings with pricing, availability, and reviews
2. **Articles/Blogs**: News articles with author, publication date, and organization
3. **Events**: Concert, conference, and meetup information
4. **Local Business**: Opening hours, location, and contact details
5. **Recipes**: Ingredients, cooking time, and nutritional information
6. **Job Postings**: Position details, requirements, and application information

## Integration

The library is designed to integrate seamlessly with:
- Laravel
- Symfony
- WordPress
- Drupal
- Standalone PHP applications

## Documentation Structure

Each output format has dedicated documentation and examples:

```
docs/
├── json-ld/
│   ├── README.md
│   ├── examples/
│   │   ├── article.php
│   │   ├── product.php
│   │   ├── event.php
│   │   ├── organization.php
│   │   └── ...
│   └── advanced/
├── microdata/
│   ├── README.md
│   ├── examples/
│   │   ├── article.html
│   │   ├── product.html
│   │   ├── event.html
│   │   └── ...
│   └── integration/
└── rdfa/
    ├── README.md
    ├── examples/
    │   ├── article.html
    │   ├── product.html
    │   └── ...
    └── best-practices/
```

## Testing and Validation

### Automated Testing Strategy

#### Unit Tests
- Test individual schema type creation
- Validate property assignment and type checking
- Ensure immutability and builder patterns work correctly

#### Integration Tests
- Test complete schema generation workflows
- Validate output against Schema.org specifications
- Cross-format consistency checks

#### Validation Testing

**Online Validators:**
1. **Schema.org Validator** (https://validator.schema.org/)
   - Official validator for testing structured data
   - Validates syntax and vocabulary compliance
   - Integration tests automatically submit to this validator

2. **Google Rich Results Test**
   - Validates schemas for Google search compatibility
   - Tests rich snippet eligibility
   - Provides preview of search results

3. **Structured Data Linter** (http://linter.structured-data.org/)
   - Open-source validator
   - Supports multiple formats (JSON-LD, Microdata, RDFa)
   - Can be self-hosted for CI/CD integration

**Open-Source Validation Tools:**
1. **structured-data-testing-tool** (npm package)
   ```bash
   npm install -g structured-data-testing-tool
   sdtt --url http://example.com --format json
   ```

2. **schema-dts** (TypeScript definitions)
   - Can be adapted for PHP validation
   - Provides complete type definitions

3. **Apache Any23**
   - Java-based library for extracting structured data
   - Can be used for validation in CI pipelines

4. **EasyRdf** (PHP Library)
   ```php
   composer require easyrdf/easyrdf
   ```
   - PHP library for RDF validation
   - Useful for RDFa output testing

### Testing Implementation

```php
// Example test structure
class SchemaValidationTest extends TestCase
{
    public function testArticleSchemaValidation()
    {
        $article = Schema::article()
            ->headline('Test Article')
            ->author(Schema::person()->name('John Doe'));
            
        // Local validation
        $this->assertTrue($article->validate());
        
        // Remote validation (for CI/CD)
        $validator = new SchemaOrgValidator();
        $result = $validator->validate($article->toJsonLd());
        $this->assertEmpty($result->getErrors());
    }
}
```

### Continuous Integration

The project includes validation in the CI/CD pipeline:
- Pre-commit hooks run local validation
- GitHub Actions/GitLab CI runs full validation suite
- Automated testing against multiple validators
- Performance benchmarks for schema generation

## Quality Assurance

### Automated Quality Gates
Every commit must pass these automated checks:
1. **Code Standards** - PHP_CodeSniffer with PSR-12
2. **Static Analysis** - PHPStan Level 9 + Psalm Level 1
3. **Tests** - 100% pass rate with >95% coverage
4. **Security** - No known vulnerabilities (Composer audit)
5. **Complexity** - Cyclomatic complexity limits enforced
6. **Documentation** - All public APIs must be documented

### Continuous Integration Pipeline
```yaml
# Every push triggers:
- PHP 8.3 compatibility check
- Full test suite execution
- Code coverage analysis
- Static analysis (PHPStan + Psalm)
- Security vulnerability scan
- Schema.org validation tests
- Performance benchmarks
```

## Standards Compliance

This library strictly adheres to:
- Schema.org vocabulary and specifications
- Google's structured data guidelines
- W3C standards for RDFa and Microdata
- PSR-4 (Autoloading), PSR-12 (Coding Style), PSR-7 (HTTP Message)
- Semantic Versioning 2.0.0
- Keep a Changelog format
- Conventional Commits specification