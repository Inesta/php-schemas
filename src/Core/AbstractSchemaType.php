<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core;

use DateTimeInterface;
use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Contracts\ValidatorInterface;
use Inesta\Schemas\Core\Exceptions\ValidationException;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationResult;
use Inesta\Schemas\Validation\Validator;

use function array_key_exists;
use function array_map;
use function is_array;

/**
 * Abstract base class for all Schema.org types.
 *
 * Provides common functionality for schema types including property management,
 * validation, and rendering to different output formats.
 */
abstract class AbstractSchemaType implements SchemaTypeInterface
{
    private static bool $strictMode = false;

    private static ?ValidatorInterface $validator = null;

    private static ?RendererInterface $jsonLdRenderer = null;

    private static ?RendererInterface $microdataRenderer = null;

    private static ?RendererInterface $rdfaRenderer = null;

    /**
     * @param array<string, mixed> $properties The schema properties
     * @param string               $context    The Schema.org context URL
     */
    public function __construct(
        protected readonly array $properties = [],
        protected readonly string $context = 'https://schema.org',
    ) {}

    /**
     * Get the Schema.org type name.
     *
     * This method must be implemented by concrete schema types.
     *
     * @return string The Schema.org type name
     */
    abstract public static function getSchemaType(): string;

    public function getType(): string
    {
        return static::getSchemaType();
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $property): mixed
    {
        return $this->properties[$property] ?? null;
    }

    public function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->properties);
    }

    public function withProperty(string $property, mixed $value): static
    {
        $properties = $this->properties;
        $properties[$property] = $value;

        $className = static::class;

        /** @var static */
        return new $className($properties, $this->context);
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function validate(): ValidationResult
    {
        $validator = self::getValidator();

        return $validator->validate($this);
    }

    public function isValid(): bool
    {
        return $this->validate()->isValid();
    }

    public function toArray(): array
    {
        $array = [
            '@context' => $this->context,
            '@type' => $this->getType(),
        ];

        foreach ($this->properties as $property => $value) {
            $array[$property] = $this->convertValueToArray($value);
        }

        return $array;
    }

    public function toJsonLd(): string
    {
        if (self::$strictMode && !$this->isValid()) {
            throw new ValidationException($this->validate());
        }

        return self::getJsonLdRenderer()->render($this);
    }

    public function toMicrodata(): string
    {
        if (self::$strictMode && !$this->isValid()) {
            throw new ValidationException($this->validate());
        }

        return self::getMicrodataRenderer()->render($this);
    }

    public function toRdfa(): string
    {
        if (self::$strictMode && !$this->isValid()) {
            throw new ValidationException($this->validate());
        }

        return self::getRdfaRenderer()->render($this);
    }

    /**
     * Convert a value to its array representation for serialization.
     *
     * @param mixed $value The value to convert
     *
     * @return mixed The converted value
     */
    private function convertValueToArray(mixed $value): mixed
    {
        if ($value instanceof SchemaTypeInterface) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map([$this, 'convertValueToArray'], $value);
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        return $value;
    }

    /**
     * Get the required properties for this schema type.
     *
     * @return array<string> Array of required property names
     */
    public static function getRequiredProperties(): array
    {
        return [];
    }

    /**
     * Get the optional properties for this schema type.
     *
     * @return array<string> Array of optional property names
     */
    public static function getOptionalProperties(): array
    {
        return [];
    }

    /**
     * Get all valid properties for this schema type.
     *
     * @return array<string> Array of all valid property names
     */
    public static function getValidProperties(): array
    {
        return [
            ...static::getRequiredProperties(),
            ...static::getOptionalProperties(),
        ];
    }

    /**
     * Enable or disable strict mode.
     *
     * In strict mode, rendering methods will throw exceptions for invalid schemas.
     *
     * @param bool $strict Whether to enable strict mode
     */
    public static function setStrictMode(bool $strict): void
    {
        self::$strictMode = $strict;
    }

    /**
     * Check if strict mode is enabled.
     *
     * @return bool True if strict mode is enabled
     */
    public static function isStrictMode(): bool
    {
        return self::$strictMode;
    }

    /**
     * Set a custom validator.
     *
     * @param ValidatorInterface $validator The validator to use
     */
    public static function setValidator(ValidatorInterface $validator): void
    {
        self::$validator = $validator;
    }

    /**
     * Get the current validator instance.
     *
     * @return ValidatorInterface The validator instance
     */
    private static function getValidator(): ValidatorInterface
    {
        if (self::$validator === null) {
            self::$validator = new Validator();
        }

        return self::$validator;
    }

    /**
     * Get the JSON-LD renderer instance.
     *
     * @return RendererInterface The JSON-LD renderer
     */
    private static function getJsonLdRenderer(): RendererInterface
    {
        if (self::$jsonLdRenderer === null) {
            self::$jsonLdRenderer = new JsonLdRenderer();
        }

        return self::$jsonLdRenderer;
    }

    /**
     * Get the Microdata renderer instance.
     *
     * @return RendererInterface The Microdata renderer
     */
    private static function getMicrodataRenderer(): RendererInterface
    {
        if (self::$microdataRenderer === null) {
            self::$microdataRenderer = new MicrodataRenderer();
        }

        return self::$microdataRenderer;
    }

    /**
     * Get the RDFa renderer instance.
     *
     * @return RendererInterface The RDFa renderer
     */
    private static function getRdfaRenderer(): RendererInterface
    {
        if (self::$rdfaRenderer === null) {
            self::$rdfaRenderer = new RdfaRenderer();
        }

        return self::$rdfaRenderer;
    }
}
