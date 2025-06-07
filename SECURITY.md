# Security Policy

## Supported Versions

We take security seriously and will provide security updates for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

If you discover a security vulnerability within the PHP Schema.org Library, please send an email to **security@inesta.com**. Do not create a public issue.

### What to include in your report

Please include the following information in your security report:

1. **Description** - A clear description of the vulnerability
2. **Impact** - What an attacker could achieve by exploiting this vulnerability
3. **Reproduction Steps** - Step-by-step instructions to reproduce the vulnerability
4. **Proof of Concept** - If possible, include a working proof of concept
5. **Suggested Fix** - If you have ideas on how to fix the vulnerability
6. **CVE Information** - If this vulnerability has been assigned a CVE, please include it

### Response Timeline

We will acknowledge receipt of your vulnerability report within **24 hours** and will send a more detailed response within **48 hours** indicating the next steps in handling your report.

We will keep you informed of the progress towards a fix and may ask for additional information or guidance.

### Disclosure Policy

When we receive a security bug report, we will:

1. **Confirm the problem** and determine the affected versions
2. **Audit code** to find any similar problems
3. **Prepare fixes** for all affected versions
4. **Release patched versions** as soon as possible
5. **Announce the vulnerability** in our security advisories

We prefer to coordinate disclosure with you to ensure users have time to update before the vulnerability is publicly announced.

### Security Best Practices

When using this library, please follow these security best practices:

#### Input Validation
- Always validate and sanitize user input before creating schemas
- Use the library's built-in validation features
- Be cautious with dynamic schema generation from untrusted sources

#### Output Escaping
The library automatically escapes output for different formats, but you should:
- Ensure your application properly handles the generated markup
- Be careful when embedding schemas in HTML contexts
- Validate generated schemas before outputting to users

#### Dependencies
- Keep the library updated to the latest version
- Regularly audit your dependencies using `composer audit`
- Use the library's security-focused configuration

#### Example Secure Usage

```php
<?php

use Inesta\Schemas\Schema;
use Inesta\Schemas\Validation\ValidationException;

// ✅ Good: Validate input and handle errors
try {
    $userInput = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    
    if (empty($userInput)) {
        throw new InvalidArgumentException('Title is required');
    }
    
    $article = Schema::article()
        ->headline($userInput)
        ->validate(); // Throws exception if invalid
        
    echo $article->toJsonLd();
} catch (ValidationException $e) {
    // Handle validation errors safely
    error_log('Schema validation failed: ' . $e->getMessage());
    // Don't expose validation details to users
}

// ❌ Bad: No validation or error handling
$article = Schema::article()
    ->headline($_POST['title']) // Unvalidated input
    ->toJsonLd(); // No validation
```

## Known Security Considerations

### XSS Prevention
- All HTML output formats (Microdata, RDFa) are automatically escaped
- JSON-LD output is safely encoded as JSON
- Custom renderers should follow the same escaping principles

### Injection Attacks
- The library is designed to prevent JSON injection
- Property values are properly escaped and validated
- Schema structure is controlled by the library, not user input

### Performance Security
- Large schemas are handled efficiently to prevent DoS attacks
- Circular reference detection prevents infinite loops
- Memory usage is optimized for production environments

## Security Testing

Our security measures include:

- **Static Analysis** - PHPStan and Psalm at the strictest levels
- **Dependency Scanning** - Regular audits with `composer audit`
- **Automated Testing** - Security-focused test cases
- **Code Review** - All changes reviewed for security implications

## Contact

For security-related questions or concerns, please contact:
- **Email**: security@inesta.com
- **PGP Key**: Available upon request

For general questions, please use the project's public issue tracker.

---

Thank you for helping to keep the PHP Schema.org Library secure!