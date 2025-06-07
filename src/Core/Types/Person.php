<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Types;

use Inesta\Schemas\Core\AbstractSchemaType;

/**
 * Represents a Person in Schema.org.
 *
 * A person (alive, dead, undead, or fictional).
 *
 * @see https://schema.org/Person
 */
final class Person extends AbstractSchemaType
{
    public static function getSchemaType(): string
    {
        return 'Person';
    }

    public static function getRequiredProperties(): array
    {
        return [];
    }

    public static function getOptionalProperties(): array
    {
        return [
            // Inherited from Thing
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
            'dateCreated',
            'dateModified',
            'datePublished',

            // Person-specific properties
            'additionalName',
            'address',
            'affiliation',
            'alumniOf',
            'award',
            'birthDate',
            'birthPlace',
            'brand',
            'children',
            'colleague',
            'contactPoint',
            'deathDate',
            'deathPlace',
            'duns',
            'email',
            'familyName',
            'faxNumber',
            'follows',
            'funder',
            'funding',
            'gender',
            'givenName',
            'globalLocationNumber',
            'hasCredential',
            'hasOccupation',
            'hasOfferCatalog',
            'hasPOS',
            'height',
            'homeLocation',
            'honorificPrefix',
            'honorificSuffix',
            'interactionStatistic',
            'isicV4',
            'jobTitle',
            'knows',
            'knowsAbout',
            'knowsLanguage',
            'makesOffer',
            'memberOf',
            'naics',
            'nationality',
            'netWorth',
            'owns',
            'parent',
            'performerIn',
            'publishingPrinciples',
            'relatedTo',
            'seeks',
            'sibling',
            'sponsor',
            'spouse',
            'taxID',
            'telephone',
            'vatID',
            'weight',
            'workLocation',
            'worksFor',
        ];
    }
}
