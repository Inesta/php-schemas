# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of the PHP Schema.org library
- Support for JSON-LD output format
- Support for Microdata output format  
- Support for RDFa output format
- Fluent API for building schemas
- Type-safe implementation with PHP 8.3+ features
- Comprehensive validation system
- Schema.org compliance checking
- Laravel integration with service provider and facade
- Symfony bundle for framework integration
- Extensive documentation and examples
- 95%+ test coverage
- Strict code quality standards (PHPStan Level 9, Psalm Level 1)

### Security
- Input sanitization for all user-provided data
- XSS prevention in HTML output formats
- Protection against circular references

[Unreleased]: https://github.com/inesta/php-schemas/compare/v1.0.0...HEAD