<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Symfony;

use Inesta\Schemas\Adapters\Symfony\DependencyInjection\SchemaExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle for Schema.org library.
 *
 * Provides integration with Symfony applications, including
 * service registration, Twig extensions, and configuration.
 */
final class SchemaBundle extends Bundle
{
    public function getContainerExtension(): SchemaExtension
    {
        if ($this->extension === null) {
            $this->extension = new SchemaExtension();
        }

        return $this->extension;
    }
}
