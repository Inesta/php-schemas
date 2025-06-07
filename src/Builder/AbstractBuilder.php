<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder;

use Inesta\Schemas\Contracts\BuilderInterface;
use Inesta\Schemas\Contracts\SchemaTypeInterface;

use function array_key_exists;
use function array_merge;
use function is_array;

/**
 * Abstract base class for schema builders.
 *
 * Provides common functionality for building schema objects with fluent interfaces.
 * Concrete builders should extend this class and implement the build() method.
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var array<string, mixed> The data being built
     */
    protected array $data = [];

    /**
     * @var string The Schema.org context URL
     */
    protected string $context = 'https://schema.org';

    /**
     * Build and return the final schema object.
     *
     * This method must be implemented by concrete builders.
     *
     * @return SchemaTypeInterface The constructed schema
     */
    abstract public function build(): SchemaTypeInterface;

    /**
     * Get the schema type class name that this builder creates.
     *
     * @return class-string<SchemaTypeInterface> The schema type class
     */
    abstract protected function getSchemaClass(): string;

    public function reset(): static
    {
        $this->data = [];
        $this->context = 'https://schema.org';

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Set the Schema.org context URL.
     *
     * @param string $context The context URL
     *
     * @return static The builder instance for method chaining
     */
    public function setContext(string $context): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the current context URL.
     *
     * @return string The context URL
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * Set a property value.
     *
     * @param string $property The property name
     * @param mixed  $value    The property value
     *
     * @return static The builder instance for method chaining
     */
    protected function setProperty(string $property, mixed $value): static
    {
        $this->data[$property] = $value;

        return $this;
    }

    /**
     * Get a property value.
     *
     * @param string $property The property name
     * @param mixed  $default  The default value if property doesn't exist
     *
     * @return mixed The property value or default
     */
    protected function getProperty(string $property, mixed $default = null): mixed
    {
        return $this->data[$property] ?? $default;
    }

    /**
     * Check if a property exists.
     *
     * @param string $property The property name
     *
     * @return bool True if the property exists
     */
    protected function hasProperty(string $property): bool
    {
        return array_key_exists($property, $this->data);
    }

    /**
     * Remove a property.
     *
     * @param string $property The property name
     *
     * @return static The builder instance for method chaining
     */
    protected function removeProperty(string $property): static
    {
        unset($this->data[$property]);

        return $this;
    }

    /**
     * Add a value to an array property.
     *
     * If the property doesn't exist, it will be created as an array.
     * If the property exists but isn't an array, it will be converted to an array.
     *
     * @param string $property The property name
     * @param mixed  $value    The value to add
     *
     * @return static The builder instance for method chaining
     */
    protected function addToProperty(string $property, mixed $value): static
    {
        if (!isset($this->data[$property])) {
            $this->data[$property] = [];
        } elseif (!is_array($this->data[$property])) {
            $this->data[$property] = [$this->data[$property]];
        }

        $this->data[$property][] = $value;

        return $this;
    }

    /**
     * Merge data into the builder.
     *
     * @param array<string, mixed> $data The data to merge
     *
     * @return static The builder instance for method chaining
     */
    protected function mergeData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Validate the current data before building.
     *
     * Override this method in concrete builders to add validation logic.
     *
     * @return bool True if data is valid
     */
    protected function validateData(): bool
    {
        return true;
    }
}
