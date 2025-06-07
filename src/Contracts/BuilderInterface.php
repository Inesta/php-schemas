<?php

declare(strict_types=1);

namespace Inesta\Schemas\Contracts;

/**
 * Interface for schema builders.
 *
 * Builders provide a fluent interface for constructing schema objects
 * using the builder pattern.
 */
interface BuilderInterface
{
    /**
     * Build and return the final schema object.
     *
     * @return SchemaTypeInterface The constructed schema
     */
    public function build(): SchemaTypeInterface;

    /**
     * Reset the builder to its initial state.
     *
     * @return static The builder instance for method chaining
     */
    public function reset(): static;

    /**
     * Get the current data being built.
     *
     * @return array<string, mixed> The current builder data
     */
    public function getData(): array;
}
