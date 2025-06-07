<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Types;

use Inesta\Schemas\Core\AbstractSchemaType;

/**
 * Represents the most generic type of item in Schema.org.
 *
 * Thing is the root of the Schema.org type hierarchy. All other types
 * inherit from Thing and add more specific properties.
 *
 * @see https://schema.org/Thing
 */
final class Thing extends AbstractSchemaType
{
    public static function getSchemaType(): string
    {
        return 'Thing';
    }

    public static function getRequiredProperties(): array
    {
        return [];
    }

    public static function getOptionalProperties(): array
    {
        return [
            'additionalType',
            'alternateName',
            'description',
            'disambiguatingDescription',
            'identifier',
            'image',
            'mainEntityOfPage',
            'name',
            'potentialAction',
            'sameAs',
            'subjectOf',
            'url',
            // Common temporal properties
            'dateCreated',
            'dateModified',
            'datePublished',
        ];
    }
}
