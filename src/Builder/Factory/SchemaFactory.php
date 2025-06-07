<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder\Factory;

use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Exceptions\SchemaException;
use Inesta\Schemas\Core\Types\Article;
use Inesta\Schemas\Core\Types\Organization;
use Inesta\Schemas\Core\Types\Person;
use Inesta\Schemas\Core\Types\Thing;
use ReflectionClass;

use function class_exists;
use function sprintf;

/**
 * Factory for creating Schema.org type instances.
 *
 * Provides a centralized way to create schema objects with proper type safety
 * and validation. Supports both direct instantiation and builder pattern creation.
 */
final class SchemaFactory
{
    /**
     * @var array<string, class-string<SchemaTypeInterface>> Registered schema type classes
     */
    private static array $typeRegistry = [
        'Thing' => Thing::class,
        'Article' => Article::class,
        'Person' => Person::class,
        'Organization' => Organization::class,
    ];

    /**
     * Create a schema object of the specified type.
     *
     * @param string               $type       The Schema.org type name
     * @param array<string, mixed> $properties Initial properties for the schema
     * @param string               $context    The Schema.org context URL
     *
     * @throws SchemaException If the type is not registered or class doesn't exist
     *
     * @return SchemaTypeInterface The created schema object
     */
    public static function create(
        string $type,
        array $properties = [],
        string $context = 'https://schema.org',
    ): SchemaTypeInterface {
        $className = self::getTypeClass($type);

        if (!class_exists($className)) {
            throw new SchemaException(sprintf('Schema type class "%s" does not exist', $className));
        }

        return new $className($properties, $context);
    }

    /**
     * Register a new schema type.
     *
     * @param string                            $type      The Schema.org type name
     * @param class-string<SchemaTypeInterface> $className The class name implementing the type
     *
     * @throws SchemaException If the class doesn't implement SchemaTypeInterface
     */
    public static function registerType(string $type, string $className): void
    {
        if (!class_exists($className)) {
            throw new SchemaException(sprintf('Class "%s" does not exist', $className));
        }

        $reflection = new ReflectionClass($className);
        if (!$reflection->implementsInterface(SchemaTypeInterface::class)) {
            throw new SchemaException(sprintf(
                'Class "%s" must implement "%s"',
                $className,
                SchemaTypeInterface::class,
            ));
        }

        self::$typeRegistry[$type] = $className;
    }

    /**
     * Check if a schema type is registered.
     *
     * @param string $type The Schema.org type name
     *
     * @return bool True if the type is registered
     */
    public static function hasType(string $type): bool
    {
        return isset(self::$typeRegistry[$type]);
    }

    /**
     * Get all registered schema types.
     *
     * @return array<string, class-string<SchemaTypeInterface>> Array of type names to class mappings
     */
    public static function getRegisteredTypes(): array
    {
        return self::$typeRegistry;
    }

    /**
     * Get the class name for a registered type.
     *
     * @param string $type The Schema.org type name
     *
     * @throws SchemaException If the type is not registered
     *
     * @return class-string<SchemaTypeInterface> The class name
     */
    public static function getTypeClass(string $type): string
    {
        if (!self::hasType($type)) {
            throw new SchemaException(sprintf('Schema type "%s" is not registered', $type));
        }

        return self::$typeRegistry[$type];
    }

    /**
     * Clear all registered types (mainly for testing).
     */
    public static function clearRegistry(): void
    {
        self::$typeRegistry = [];
    }

    /**
     * Reset the registry to default types.
     */
    public static function resetRegistry(): void
    {
        self::$typeRegistry = [
            'Thing' => Thing::class,
            'Article' => Article::class,
            'Person' => Person::class,
            'Organization' => Organization::class,
        ];
    }
}
