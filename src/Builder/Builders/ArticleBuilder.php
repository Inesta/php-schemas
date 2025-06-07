<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder\Builders;

use DateTimeInterface;
use Inesta\Schemas\Builder\AbstractBuilder;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Article;

/**
 * Builder for Article schema objects.
 *
 * Provides a fluent interface for constructing Article instances.
 */
final class ArticleBuilder extends AbstractBuilder
{
    public function build(): SchemaTypeInterface
    {
        return new Article($this->data, $this->context);
    }

    /**
     * Set the headline property (required).
     *
     * @param string $headline The article headline
     *
     * @return static The builder instance for method chaining
     */
    public function headline(string $headline): static
    {
        return $this->setProperty('headline', $headline);
    }

    /**
     * Set the alternative headline property.
     *
     * @param string $alternativeHeadline The alternative headline
     *
     * @return static The builder instance for method chaining
     */
    public function alternativeHeadline(string $alternativeHeadline): static
    {
        return $this->setProperty('alternativeHeadline', $alternativeHeadline);
    }

    /**
     * Set the article body property.
     *
     * @param string $articleBody The article body content
     *
     * @return static The builder instance for method chaining
     */
    public function articleBody(string $articleBody): static
    {
        return $this->setProperty('articleBody', $articleBody);
    }

    /**
     * Set the author property.
     *
     * @param mixed $author The author (Person or Organization)
     *
     * @return static The builder instance for method chaining
     */
    public function author(mixed $author): static
    {
        return $this->setProperty('author', $author);
    }

    /**
     * Set the publisher property.
     *
     * @param mixed $publisher The publisher (Person or Organization)
     *
     * @return static The builder instance for method chaining
     */
    public function publisher(mixed $publisher): static
    {
        return $this->setProperty('publisher', $publisher);
    }

    /**
     * Set the date published property.
     *
     * @param DateTimeInterface $datePublished The date published
     *
     * @return static The builder instance for method chaining
     */
    public function datePublished(DateTimeInterface $datePublished): static
    {
        return $this->setProperty('datePublished', $datePublished);
    }

    /**
     * Set the date modified property.
     *
     * @param DateTimeInterface $dateModified The date modified
     *
     * @return static The builder instance for method chaining
     */
    public function dateModified(DateTimeInterface $dateModified): static
    {
        return $this->setProperty('dateModified', $dateModified);
    }

    /**
     * Set the article section property.
     *
     * @param string $articleSection The article section
     *
     * @return static The builder instance for method chaining
     */
    public function articleSection(string $articleSection): static
    {
        return $this->setProperty('articleSection', $articleSection);
    }

    /**
     * Set the word count property.
     *
     * @param int $wordCount The word count
     *
     * @return static The builder instance for method chaining
     */
    public function wordCount(int $wordCount): static
    {
        return $this->setProperty('wordCount', $wordCount);
    }

    /**
     * Set the keywords property.
     *
     * @param array<string>|string $keywords The keywords
     *
     * @return static The builder instance for method chaining
     */
    public function keywords(array|string $keywords): static
    {
        return $this->setProperty('keywords', $keywords);
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
     * @param string|array<string> $image The image URL or array of URLs
     *
     * @return static The builder instance for method chaining
     */
    public function image(array|string $image): static
    {
        return $this->setProperty('image', $image);
    }

    protected function getSchemaClass(): string
    {
        return Article::class;
    }
}
