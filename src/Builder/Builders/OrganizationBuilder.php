<?php

declare(strict_types=1);

namespace Inesta\Schemas\Builder\Builders;

use DateTimeInterface;
use Inesta\Schemas\Builder\AbstractBuilder;
use Inesta\Schemas\Contracts\SchemaTypeInterface;
use Inesta\Schemas\Core\Types\Organization;

/**
 * Builder for Organization schema objects.
 *
 * Provides a fluent interface for constructing Organization instances.
 */
final class OrganizationBuilder extends AbstractBuilder
{
    public function build(): SchemaTypeInterface
    {
        return new Organization($this->data, $this->context);
    }

    /**
     * Set the name property.
     *
     * @param string $name The organization name
     *
     * @return static The builder instance for method chaining
     */
    public function name(string $name): static
    {
        return $this->setProperty('name', $name);
    }

    /**
     * Set the legal name property.
     *
     * @param string $legalName The legal name
     *
     * @return static The builder instance for method chaining
     */
    public function legalName(string $legalName): static
    {
        return $this->setProperty('legalName', $legalName);
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
     * @param string $url The organization URL
     *
     * @return static The builder instance for method chaining
     */
    public function url(string $url): static
    {
        return $this->setProperty('url', $url);
    }

    /**
     * Set the logo property.
     *
     * @param string $logo The logo URL
     *
     * @return static The builder instance for method chaining
     */
    public function logo(string $logo): static
    {
        return $this->setProperty('logo', $logo);
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
     * Set the founding date property.
     *
     * @param DateTimeInterface $foundingDate The founding date
     *
     * @return static The builder instance for method chaining
     */
    public function foundingDate(DateTimeInterface $foundingDate): static
    {
        return $this->setProperty('foundingDate', $foundingDate);
    }

    /**
     * Set the founding location property.
     *
     * @param string $foundingLocation The founding location
     *
     * @return static The builder instance for method chaining
     */
    public function foundingLocation(string $foundingLocation): static
    {
        return $this->setProperty('foundingLocation', $foundingLocation);
    }

    /**
     * Set the number of employees property.
     *
     * @param int $numberOfEmployees The number of employees
     *
     * @return static The builder instance for method chaining
     */
    public function numberOfEmployees(int $numberOfEmployees): static
    {
        return $this->setProperty('numberOfEmployees', $numberOfEmployees);
    }

    /**
     * Set the tax ID property.
     *
     * @param string $taxID The tax ID
     *
     * @return static The builder instance for method chaining
     */
    public function taxID(string $taxID): static
    {
        return $this->setProperty('taxID', $taxID);
    }

    /**
     * Set the DUNS property.
     *
     * @param string $duns The DUNS number
     *
     * @return static The builder instance for method chaining
     */
    public function duns(string $duns): static
    {
        return $this->setProperty('duns', $duns);
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
     * Set the contact point property.
     *
     * @param mixed $contactPoint The contact point
     *
     * @return static The builder instance for method chaining
     */
    public function contactPoint(mixed $contactPoint): static
    {
        return $this->setProperty('contactPoint', $contactPoint);
    }

    /**
     * Set the area served property.
     *
     * @param string $areaServed The area served
     *
     * @return static The builder instance for method chaining
     */
    public function areaServed(string $areaServed): static
    {
        return $this->setProperty('areaServed', $areaServed);
    }

    /**
     * Set the slogan property.
     *
     * @param string $slogan The organization slogan
     *
     * @return static The builder instance for method chaining
     */
    public function slogan(string $slogan): static
    {
        return $this->setProperty('slogan', $slogan);
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
     * Set the parent organization property.
     *
     * @param mixed $parentOrganization The parent organization
     *
     * @return static The builder instance for method chaining
     */
    public function parentOrganization(mixed $parentOrganization): static
    {
        return $this->setProperty('parentOrganization', $parentOrganization);
    }

    /**
     * Add a sub-organization.
     *
     * @param mixed $subOrganization The sub-organization
     *
     * @return static The builder instance for method chaining
     */
    public function subOrganization(mixed $subOrganization): static
    {
        return $this->addToProperty('subOrganization', $subOrganization);
    }

    /**
     * Add a department.
     *
     * @param mixed $department The department
     *
     * @return static The builder instance for method chaining
     */
    public function department(mixed $department): static
    {
        return $this->addToProperty('department', $department);
    }

    protected function getSchemaClass(): string
    {
        return Organization::class;
    }
}
