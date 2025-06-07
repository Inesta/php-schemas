<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Inesta\Schemas\Adapters\Laravel\SchemaManager;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Validation\ValidationResult;

/**
 * Laravel facade for Schema.org operations.
 *
 * Provides static access to schema creation, validation, and rendering
 * functionality within Laravel applications.
 *
 * @method static SchemaTypeInterface create(string $type, array $properties = [], string $context = 'https://schema.org')
 * @method static SchemaTypeInterface article(array $properties = [])
 * @method static SchemaTypeInterface person(array $properties = [])
 * @method static SchemaTypeInterface organization(array $properties = [])
 * @method static SchemaTypeInterface thing(array $properties = [])
 * @method static ValidationResult    validate(SchemaTypeInterface $schema)
 * @method static string              render(SchemaTypeInterface $schema)
 * @method static string              renderJsonLd(SchemaTypeInterface $schema, bool $scriptTag = true, bool $prettyPrint = true)
 * @method static string              renderMicrodata(SchemaTypeInterface $schema, bool $semanticElements = true, bool $metaElements = true)
 * @method static string              renderRdfa(SchemaTypeInterface $schema, bool $semanticElements = true, bool $prettyPrint = true)
 *
 * @see SchemaManager
 */
final class Schema extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string The facade accessor
     */
    protected static function getFacadeAccessor(): string
    {
        return SchemaManager::class;
    }
}
