<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder\Builders;

use Inesta\Schemas\Builder\AbstractBuilder;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Thing;

/**
 * Builder for Thing schema objects.
 *
 * Provides a fluent interface for constructing Thing instances.
 */
final class ThingBuilder extends AbstractBuilder
{
    public function build(): SchemaTypeInterface
    {
        return new Thing($this->data, $this->context);
    }

    /**
     * Set the name property.
     *
     * @param string $name The name
     *
     * @return static The builder instance for method chaining
     */
    public function name(string $name): static
    {
        return $this->setProperty('name', $name);
    }

    /**
     * Set the description property.
     *
     * @param string $description The description
     *
     * @return static The builder instance for method chaining
     */
    public function description(string $description): static
    {
        return $this->setProperty('description', $description);
    }

    /**
     * Set the URL property.
     *
     * @param string $url The URL
     *
     * @return static The builder instance for method chaining
     */
    public function url(string $url): static
    {
        return $this->setProperty('url', $url);
    }

    /**
     * Set the image property.
     *
     * @param string $image The image URL
     *
     * @return static The builder instance for method chaining
     */
    public function image(string $image): static
    {
        return $this->setProperty('image', $image);
    }

    /**
     * Set the identifier property.
     *
     * @param string $identifier The identifier
     *
     * @return static The builder instance for method chaining
     */
    public function identifier(string $identifier): static
    {
        return $this->setProperty('identifier', $identifier);
    }

    /**
     * Set the alternateName property.
     *
     * @param string $alternateName The alternate name
     *
     * @return static The builder instance for method chaining
     */
    public function alternateName(string $alternateName): static
    {
        return $this->setProperty('alternateName', $alternateName);
    }

    /**
     * Set the disambiguatingDescription property.
     *
     * @param string $disambiguatingDescription The disambiguating description
     *
     * @return static The builder instance for method chaining
     */
    public function disambiguatingDescription(string $disambiguatingDescription): static
    {
        return $this->setProperty('disambiguatingDescription', $disambiguatingDescription);
    }

    /**
     * Set the mainEntityOfPage property.
     *
     * @param string $mainEntityOfPage The main entity of page URL
     *
     * @return static The builder instance for method chaining
     */
    public function mainEntityOfPage(string $mainEntityOfPage): static
    {
        return $this->setProperty('mainEntityOfPage', $mainEntityOfPage);
    }

    /**
     * Add a sameAs URL.
     *
     * @param string $sameAs The sameAs URL
     *
     * @return static The builder instance for method chaining
     */
    public function sameAs(string $sameAs): static
    {
        return $this->addToProperty('sameAs', $sameAs);
    }

    /**
     * Set the subjectOf property.
     *
     * @param mixed $subjectOf The subject of
     *
     * @return static The builder instance for method chaining
     */
    public function subjectOf(mixed $subjectOf): static
    {
        return $this->setProperty('subjectOf', $subjectOf);
    }

    /**
     * Set the additionalType property.
     *
     * @param string $additionalType The additional type URL
     *
     * @return static The builder instance for method chaining
     */
    public function additionalType(string $additionalType): static
    {
        return $this->setProperty('additionalType', $additionalType);
    }

    /**
     * Add a potential action.
     *
     * @param mixed $potentialAction The potential action
     *
     * @return static The builder instance for method chaining
     */
    public function potentialAction(mixed $potentialAction): static
    {
        return $this->addToProperty('potentialAction', $potentialAction);
    }

    protected function getSchemaClass(): string
    {
        return Thing::class;
    }
}
