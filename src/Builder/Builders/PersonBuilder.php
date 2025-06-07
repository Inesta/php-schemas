<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder\Builders;

use DateTimeInterface;
use Inesta\Schemas\Builder\AbstractBuilder;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Person;

/**
 * Builder for Person schema objects.
 *
 * Provides a fluent interface for constructing Person instances.
 */
final class PersonBuilder extends AbstractBuilder
{
    public function build(): SchemaTypeInterface
    {
        return new Person($this->data, $this->context);
    }

    /**
     * Set the name property.
     *
     * @param string $name The person's name
     *
     * @return static The builder instance for method chaining
     */
    public function name(string $name): static
    {
        return $this->setProperty('name', $name);
    }

    /**
     * Set the given name property.
     *
     * @param string $givenName The given name (first name)
     *
     * @return static The builder instance for method chaining
     */
    public function givenName(string $givenName): static
    {
        return $this->setProperty('givenName', $givenName);
    }

    /**
     * Set the family name property.
     *
     * @param string $familyName The family name (last name)
     *
     * @return static The builder instance for method chaining
     */
    public function familyName(string $familyName): static
    {
        return $this->setProperty('familyName', $familyName);
    }

    /**
     * Set the additional name property.
     *
     * @param string $additionalName The additional name (middle name)
     *
     * @return static The builder instance for method chaining
     */
    public function additionalName(string $additionalName): static
    {
        return $this->setProperty('additionalName', $additionalName);
    }

    /**
     * Set the email property.
     *
     * @param string $email The email address
     *
     * @return static The builder instance for method chaining
     */
    public function email(string $email): static
    {
        return $this->setProperty('email', $email);
    }

    /**
     * Set the telephone property.
     *
     * @param string $telephone The telephone number
     *
     * @return static The builder instance for method chaining
     */
    public function telephone(string $telephone): static
    {
        return $this->setProperty('telephone', $telephone);
    }

    /**
     * Set the job title property.
     *
     * @param string $jobTitle The job title
     *
     * @return static The builder instance for method chaining
     */
    public function jobTitle(string $jobTitle): static
    {
        return $this->setProperty('jobTitle', $jobTitle);
    }

    /**
     * Set the birth date property.
     *
     * @param DateTimeInterface $birthDate The birth date
     *
     * @return static The builder instance for method chaining
     */
    public function birthDate(DateTimeInterface $birthDate): static
    {
        return $this->setProperty('birthDate', $birthDate);
    }

    /**
     * Set the gender property.
     *
     * @param string $gender The gender
     *
     * @return static The builder instance for method chaining
     */
    public function gender(string $gender): static
    {
        return $this->setProperty('gender', $gender);
    }

    /**
     * Set the nationality property.
     *
     * @param string $nationality The nationality
     *
     * @return static The builder instance for method chaining
     */
    public function nationality(string $nationality): static
    {
        return $this->setProperty('nationality', $nationality);
    }

    /**
     * Set the address property.
     *
     * @param mixed $address The address (PostalAddress or string)
     *
     * @return static The builder instance for method chaining
     */
    public function address(mixed $address): static
    {
        return $this->setProperty('address', $address);
    }

    /**
     * Set the works for property.
     *
     * @param mixed $worksFor The organization the person works for
     *
     * @return static The builder instance for method chaining
     */
    public function worksFor(mixed $worksFor): static
    {
        return $this->setProperty('worksFor', $worksFor);
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
     * Set the honorific prefix property.
     *
     * @param string $honorificPrefix The honorific prefix (Mr., Dr., etc.)
     *
     * @return static The builder instance for method chaining
     */
    public function honorificPrefix(string $honorificPrefix): static
    {
        return $this->setProperty('honorificPrefix', $honorificPrefix);
    }

    /**
     * Set the honorific suffix property.
     *
     * @param string $honorificSuffix The honorific suffix (Jr., Sr., etc.)
     *
     * @return static The builder instance for method chaining
     */
    public function honorificSuffix(string $honorificSuffix): static
    {
        return $this->setProperty('honorificSuffix', $honorificSuffix);
    }

    /**
     * Add a skill or knowledge area.
     *
     * @param mixed $knowsAbout The skill or knowledge area
     *
     * @return static The builder instance for method chaining
     */
    public function knowsAbout(mixed $knowsAbout): static
    {
        return $this->addToProperty('knowsAbout', $knowsAbout);
    }

    protected function getSchemaClass(): string
    {
        return Person::class;
    }
}
