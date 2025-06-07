<?php

declare(strict_types=1);

/**
 * IDE Helper file for PHP Schema.org Library
 * 
 * This file provides IDE autocomplete and type hints for the Schema.org library.
 * It should not be included in your application but helps with development.
 * 
 * @see https://github.com/inesta/php-schemas
 */

namespace Inesta\Schemas {

    use Inesta\Schemas\Contracts\SchemaTypeInterface;
    use Inesta\Schemas\Core\Types\Thing;
    use Inesta\Schemas\Core\Types\Article;
    use Inesta\Schemas\Core\Types\Person;
    use Inesta\Schemas\Core\Types\Organization;

    /**
     * Schema factory helper for IDE autocomplete
     */
    class Schema
    {
        /**
         * Create a Thing schema
         *
         * @param array<string, mixed> $properties
         * @return Thing
         */
        public static function thing(array $properties = []): Thing {}

        /**
         * Create an Article schema
         *
         * @param array<string, mixed> $properties
         * @return Article
         */
        public static function article(array $properties = []): Article {}

        /**
         * Create a Person schema
         *
         * @param array<string, mixed> $properties
         * @return Person
         */
        public static function person(array $properties = []): Person {}

        /**
         * Create an Organization schema
         *
         * @param array<string, mixed> $properties
         * @return Organization
         */
        public static function organization(array $properties = []): Organization {}

        /**
         * Create any schema type
         *
         * @param string $type
         * @param array<string, mixed> $properties
         * @param string $context
         * @return SchemaTypeInterface
         */
        public static function create(string $type, array $properties = [], string $context = 'https://schema.org'): SchemaTypeInterface {}

        /**
         * Validate a schema
         *
         * @param SchemaTypeInterface $schema
         * @return \Inesta\Schemas\Validation\ValidationResult
         */
        public static function validate(SchemaTypeInterface $schema): \Inesta\Schemas\Validation\ValidationResult {}

        /**
         * Render schema as JSON-LD
         *
         * @param SchemaTypeInterface $schema
         * @param bool $scriptTag
         * @param bool $prettyPrint
         * @return string
         */
        public static function renderJsonLd(SchemaTypeInterface $schema, bool $scriptTag = true, bool $prettyPrint = true): string {}

        /**
         * Render schema as Microdata
         *
         * @param SchemaTypeInterface $schema
         * @param bool $semanticElements
         * @param bool $metaElements
         * @return string
         */
        public static function renderMicrodata(SchemaTypeInterface $schema, bool $semanticElements = true, bool $metaElements = true): string {}

        /**
         * Render schema as RDFa
         *
         * @param SchemaTypeInterface $schema
         * @param bool $semanticElements
         * @param bool $prettyPrint
         * @return string
         */
        public static function renderRdfa(SchemaTypeInterface $schema, bool $semanticElements = true, bool $prettyPrint = true): string {}
    }
}

namespace Inesta\Schemas\Builder\Builders {

    use DateTimeInterface;
    use Inesta\Schemas\Contracts\SchemaTypeInterface;

    /**
     * Article Builder IDE Helper
     */
    class ArticleBuilder
    {
        /**
         * Set the headline
         */
        public function headline(string $headline): static {}

        /**
         * Set the description
         */
        public function description(string $description): static {}

        /**
         * Set the author
         */
        public function author(string|SchemaTypeInterface $author): static {}

        /**
         * Set the publisher
         */
        public function publisher(SchemaTypeInterface $publisher): static {}

        /**
         * Set the date published
         */
        public function datePublished(DateTimeInterface $datePublished): static {}

        /**
         * Set the date modified
         */
        public function dateModified(DateTimeInterface $dateModified): static {}

        /**
         * Set the keywords
         */
        public function keywords(array $keywords): static {}

        /**
         * Set the article body
         */
        public function articleBody(string $articleBody): static {}

        /**
         * Set the word count
         */
        public function wordCount(int $wordCount): static {}

        /**
         * Set the article section
         */
        public function articleSection(string $articleSection): static {}

        /**
         * Set the URL
         */
        public function url(string $url): static {}

        /**
         * Set the image
         */
        public function image(string|array $image): static {}

        /**
         * Build the Article
         */
        public function build(): \Inesta\Schemas\Core\Types\Article {}
    }

    /**
     * Person Builder IDE Helper
     */
    class PersonBuilder
    {
        /**
         * Set the name
         */
        public function name(string $name): static {}

        /**
         * Set the email
         */
        public function email(string $email): static {}

        /**
         * Set the URL
         */
        public function url(string $url): static {}

        /**
         * Set the job title
         */
        public function jobTitle(string $jobTitle): static {}

        /**
         * Set the organization the person works for
         */
        public function worksFor(SchemaTypeInterface $organization): static {}

        /**
         * Set the telephone
         */
        public function telephone(string $telephone): static {}

        /**
         * Set the address
         */
        public function address(string|SchemaTypeInterface $address): static {}

        /**
         * Set the birth date
         */
        public function birthDate(DateTimeInterface $birthDate): static {}

        /**
         * Set the image
         */
        public function image(string|array $image): static {}

        /**
         * Set the same as URLs
         */
        public function sameAs(array $sameAs): static {}

        /**
         * Build the Person
         */
        public function build(): \Inesta\Schemas\Core\Types\Person {}
    }

    /**
     * Organization Builder IDE Helper
     */
    class OrganizationBuilder
    {
        /**
         * Set the name
         */
        public function name(string $name): static {}

        /**
         * Set the URL
         */
        public function url(string $url): static {}

        /**
         * Set the logo
         */
        public function logo(string $logo): static {}

        /**
         * Set the description
         */
        public function description(string $description): static {}

        /**
         * Set the email
         */
        public function email(string $email): static {}

        /**
         * Set the telephone
         */
        public function telephone(string $telephone): static {}

        /**
         * Set the address
         */
        public function address(string|SchemaTypeInterface $address): static {}

        /**
         * Set the founding date
         */
        public function foundingDate(DateTimeInterface $foundingDate): static {}

        /**
         * Set the founder
         */
        public function founder(SchemaTypeInterface $founder): static {}

        /**
         * Set the same as URLs
         */
        public function sameAs(array $sameAs): static {}

        /**
         * Build the Organization
         */
        public function build(): \Inesta\Schemas\Core\Types\Organization {}
    }

    /**
     * Thing Builder IDE Helper
     */
    class ThingBuilder
    {
        /**
         * Set the name
         */
        public function name(string $name): static {}

        /**
         * Set the description
         */
        public function description(string $description): static {}

        /**
         * Set the URL
         */
        public function url(string $url): static {}

        /**
         * Set the image
         */
        public function image(string|array $image): static {}

        /**
         * Set the same as URLs
         */
        public function sameAs(array $sameAs): static {}

        /**
         * Set the identifier
         */
        public function identifier(string $identifier): static {}

        /**
         * Set the alternate name
         */
        public function alternateName(string $alternateName): static {}

        /**
         * Build the Thing
         */
        public function build(): \Inesta\Schemas\Core\Types\Thing {}
    }
}

// Global helper functions
namespace {
    if (!function_exists('schema')) {
        /**
         * Helper function to create schemas
         *
         * @param string $type
         * @param array<string, mixed> $properties
         * @return \Inesta\Schemas\Contracts\SchemaTypeInterface
         */
        function schema(string $type, array $properties = []): \Inesta\Schemas\Contracts\SchemaTypeInterface {
            return \Inesta\Schemas\Builder\Factory\SchemaFactory::create($type, $properties);
        }
    }

    if (!function_exists('json_ld')) {
        /**
         * Helper function to render schema as JSON-LD
         *
         * @param \Inesta\Schemas\Contracts\SchemaTypeInterface $schema
         * @param bool $scriptTag
         * @param bool $prettyPrint
         * @return string
         */
        function json_ld(\Inesta\Schemas\Contracts\SchemaTypeInterface $schema, bool $scriptTag = true, bool $prettyPrint = true): string {
            $renderer = new \Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer();
            $renderer->setIncludeScriptTag($scriptTag)->setPrettyPrint($prettyPrint);
            return $renderer->render($schema);
        }
    }

    if (!function_exists('microdata')) {
        /**
         * Helper function to render schema as Microdata
         *
         * @param \Inesta\Schemas\Contracts\SchemaTypeInterface $schema
         * @param bool $semanticElements
         * @param bool $metaElements
         * @return string
         */
        function microdata(\Inesta\Schemas\Contracts\SchemaTypeInterface $schema, bool $semanticElements = true, bool $metaElements = true): string {
            $renderer = new \Inesta\Schemas\Renderer\Microdata\MicrodataRenderer();
            $renderer->setUseSemanticElements($semanticElements)->setIncludeMetaElements($metaElements);
            return $renderer->render($schema);
        }
    }

    if (!function_exists('rdfa')) {
        /**
         * Helper function to render schema as RDFa
         *
         * @param \Inesta\Schemas\Contracts\SchemaTypeInterface $schema
         * @param bool $semanticElements
         * @param bool $prettyPrint
         * @return string
         */
        function rdfa(\Inesta\Schemas\Contracts\SchemaTypeInterface $schema, bool $semanticElements = true, bool $prettyPrint = true): string {
            $renderer = new \Inesta\Schemas\Renderer\Rdfa\RdfaRenderer();
            $renderer->setUseSemanticElements($semanticElements)->setPrettyPrint($prettyPrint);
            return $renderer->render($schema);
        }
    }
}

