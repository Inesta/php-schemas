<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Types;

use Inesta\Schemas\Core\AbstractSchemaType;

/**
 * Represents an Organization in Schema.org.
 *
 * An organization such as a school, NGO, corporation, club, etc.
 *
 * @see https://schema.org/Organization
 */
final class Organization extends AbstractSchemaType
{
    public static function getSchemaType(): string
    {
        return 'Organization';
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

            // Organization-specific properties
            'actionableFeedbackPolicy',
            'address',
            'aggregateRating',
            'alumni',
            'areaServed',
            'award',
            'brand',
            'contactPoint',
            'correctionsPolicy',
            'department',
            'dissolutionDate',
            'diversityPolicy',
            'diversityStaffingReport',
            'duns',
            'email',
            'employee',
            'ethicsPolicy',
            'event',
            'faxNumber',
            'founder',
            'foundingDate',
            'foundingLocation',
            'funder',
            'funding',
            'globalLocationNumber',
            'hasCredential',
            'hasMerchantReturnPolicy',
            'hasOfferCatalog',
            'hasPOS',
            'interactionStatistic',
            'isicV4',
            'keywords',
            'knowsAbout',
            'knowsLanguage',
            'legalName',
            'leiCode',
            'location',
            'logo',
            'makesOffer',
            'member',
            'memberOf',
            'naics',
            'nonprofitStatus',
            'numberOfEmployees',
            'ownershipFundingInfo',
            'owns',
            'parentOrganization',
            'publishingPrinciples',
            'review',
            'seeks',
            'serviceArea',
            'slogan',
            'sponsor',
            'subOrganization',
            'taxID',
            'telephone',
            'unnamedSourcesPolicy',
            'vatID',
        ];
    }
}
