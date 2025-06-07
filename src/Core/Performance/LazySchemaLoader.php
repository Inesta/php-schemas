<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Performance;

use BadMethodCallException;
use Closure;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Lazy loading implementation for schema objects.
 *
 * Defers creation of schema objects until they are actually needed.
 */
final class LazySchemaLoader implements SchemaTypeInterface
{
    private ?SchemaTypeInterface $schema = null;

    /**
     * @param Closure(): SchemaTypeInterface $loader
     */
    public function __construct(
        private readonly Closure $loader,
    ) {}

    public function getType(): string
    {
        return $this->getSchema()->getType();
    }

    public function getProperties(): array
    {
        return $this->getSchema()->getProperties();
    }

    public function getProperty(string $property): mixed
    {
        return $this->getSchema()->getProperty($property);
    }

    public function hasProperty(string $property): bool
    {
        return $this->getSchema()->hasProperty($property);
    }

    public function withProperty(string $property, mixed $value): static
    {
        // Return a new lazy loader that applies the property change
        return new self(fn (): SchemaTypeInterface => $this->getSchema()->withProperty($property, $value));
    }

    public function getContext(): string
    {
        return $this->getSchema()->getContext();
    }

    public function validate(): ValidationResult
    {
        return $this->getSchema()->validate();
    }

    public function isValid(): bool
    {
        return $this->getSchema()->isValid();
    }

    public function toArray(): array
    {
        return $this->getSchema()->toArray();
    }

    public function toJsonLd(): string
    {
        return $this->getSchema()->toJsonLd();
    }

    public function toMicrodata(): string
    {
        return $this->getSchema()->toMicrodata();
    }

    public function toRdfa(): string
    {
        return $this->getSchema()->toRdfa();
    }

    /**
     * Check if the schema has been loaded.
     */
    public function isLoaded(): bool
    {
        return $this->schema !== null;
    }

    /**
     * Force loading of the schema.
     */
    public function load(): SchemaTypeInterface
    {
        return $this->getSchema();
    }

    /**
     * Get the actual schema instance, loading it if necessary.
     */
    private function getSchema(): SchemaTypeInterface
    {
        if ($this->schema === null) {
            $loader = $this->loader;
            $this->schema = $loader();
        }

        return $this->schema;
    }

    public static function getSchemaType(): string
    {
        // This method doesn't make sense for a lazy loader since it's type-agnostic
        // It will delegate to the actual schema when loaded
        throw new BadMethodCallException('getSchemaType() cannot be called on a lazy loader. Use getType() instead.');
    }
}
