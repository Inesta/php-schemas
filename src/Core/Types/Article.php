<?php

declare(strict_types=1);

namespace Inesta\Schemas\Core\Types;

use Inesta\Schemas\Core\AbstractSchemaType;

/**
 * Represents an Article in Schema.org.
 *
 * An article, such as a news article or piece of investigative report.
 * Newspapers and magazines have articles of many different types.
 *
 * @see https://schema.org/Article
 */
final class Article extends AbstractSchemaType
{
    public static function getSchemaType(): string
    {
        return 'Article';
    }

    public static function getRequiredProperties(): array
    {
        return [
            'headline',
        ];
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

            // Article-specific properties
            'about',
            'abstract',
            'accessMode',
            'accessModeSufficient',
            'accessibilityAPI',
            'accessibilityControl',
            'accessibilityFeature',
            'accessibilityHazard',
            'accessibilitySummary',
            'accountablePerson',
            'aggregateRating',
            'alternativeHeadline',
            'articleBody',
            'articleSection',
            'associatedMedia',
            'audience',
            'audio',
            'author',
            'award',
            'character',
            'citation',
            'comment',
            'commentCount',
            'conditionsOfAccess',
            'contentLocation',
            'contentRating',
            'contentReferenceTime',
            'contributor',
            'copyrightHolder',
            'copyrightNotice',
            'copyrightYear',
            'correction',
            'countryOfOrigin',
            'creativeWorkStatus',
            'creator',
            'creditText',
            'dateModified',
            'datePublished',
            'discussionUrl',
            'editEIDR',
            'editor',
            'educationalAlignment',
            'educationalLevel',
            'educationalUse',
            'encoding',
            'encodingFormat',
            'exampleOfWork',
            'expires',
            'funder',
            'funding',
            'genre',
            'hasPart',
            'headline',
            'inLanguage',
            'interactionStatistic',
            'interactivityType',
            'interpretedAsClaim',
            'isAccessibleForFree',
            'isBasedOn',
            'isFamilyFriendly',
            'isPartOf',
            'keywords',
            'learningResourceType',
            'license',
            'locationCreated',
            'mainEntity',
            'maintainer',
            'material',
            'materialExtent',
            'mentions',
            'offers',
            'pageEnd',
            'pageStart',
            'pagination',
            'pattern',
            'position',
            'producer',
            'provider',
            'publication',
            'publisher',
            'publisherImprint',
            'publishingPrinciples',
            'recordedAt',
            'releasedEvent',
            'review',
            'schemaVersion',
            'sdDatePublished',
            'sdLicense',
            'sdPublisher',
            'size',
            'sourceOrganization',
            'spatial',
            'spatialCoverage',
            'sponsor',
            'teachingOutcome',
            'temporal',
            'temporalCoverage',
            'text',
            'thumbnailUrl',
            'timeRequired',
            'translationOfWork',
            'translator',
            'typicalAgeRange',
            'usageInfo',
            'version',
            'video',
            'workExample',
            'workTranslation',
            'wordCount',
        ];
    }
}
