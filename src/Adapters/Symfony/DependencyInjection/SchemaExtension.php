<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Symfony\DependencyInjection;

use Inesta\Schemas\Adapters\Symfony\SchemaManager;
use Inesta\Schemas\Adapters\Symfony\Twig\SchemaExtension as TwigSchemaExtension;
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationEngine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

use function class_exists;

/**
 * Symfony DI extension for Schema.org library.
 *
 * Registers services and configuration for the Schema.org library
 * within Symfony's dependency injection container.
 */
final class SchemaExtension extends Extension
{
    /**
     * Load configuration and register services.
     *
     * @param array<mixed>     $configs   The configuration array
     * @param ContainerBuilder $container The container builder
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Load default configuration
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Register core services
        $this->registerCoreServices($container, $config);

        // Register renderers
        $this->registerRenderers($container, $config);

        // Register validation engine
        $this->registerValidationEngine($container, $config);

        // Register schema manager
        $this->registerSchemaManager($container);

        // Register Twig extension if Twig is available
        if (class_exists('Twig\Environment')) {
            $this->registerTwigExtension($container);
        }
    }

    /**
     * Get the extension alias.
     *
     * @return string The extension alias
     */
    public function getAlias(): string
    {
        return 'schema';
    }

    /**
     * Register core services.
     *
     * @param ContainerBuilder $container The container builder
     * @param array<mixed>     $config    The processed configuration
     */
    private function registerCoreServices(ContainerBuilder $container, array $config): void
    {
        // Register schema factory
        $factoryDefinition = new Definition(SchemaFactory::class);
        $factoryDefinition->setPublic(false);
        $container->setDefinition(SchemaFactory::class, $factoryDefinition);
        $container->setAlias('schema.factory', SchemaFactory::class);
    }

    /**
     * Register renderer services.
     *
     * @param ContainerBuilder $container The container builder
     * @param array<mixed>     $config    The processed configuration
     */
    private function registerRenderers(ContainerBuilder $container, array $config): void
    {
        // JSON-LD Renderer
        $jsonLdDefinition = new Definition(JsonLdRenderer::class);
        $jsonLdDefinition->setPublic(false);
        if (isset($config['json_ld'])) {
            $jsonLdDefinition->addMethodCall('setPrettyPrint', [$config['json_ld']['pretty_print']]);
            $jsonLdDefinition->addMethodCall('setIncludeScriptTag', [$config['json_ld']['include_script_tag']]);
            $jsonLdDefinition->addMethodCall('setUnescapeSlashes', [$config['json_ld']['unescape_slashes']]);
            $jsonLdDefinition->addMethodCall('setUnescapeUnicode', [$config['json_ld']['unescape_unicode']]);
            $jsonLdDefinition->addMethodCall('setCompactOutput', [$config['json_ld']['compact_output']]);
        }
        $container->setDefinition(JsonLdRenderer::class, $jsonLdDefinition);
        $container->setAlias('schema.renderer.json_ld', JsonLdRenderer::class);

        // Microdata Renderer
        $microdataDefinition = new Definition(MicrodataRenderer::class);
        $microdataDefinition->setPublic(false);
        if (isset($config['microdata'])) {
            $microdataDefinition->addMethodCall('setPrettyPrint', [$config['microdata']['pretty_print']]);
            $microdataDefinition->addMethodCall('setUseSemanticElements', [$config['microdata']['use_semantic_elements']]);
            $microdataDefinition->addMethodCall('setIncludeMetaElements', [$config['microdata']['include_meta_elements']]);
            $microdataDefinition->addMethodCall('setContainerElement', [$config['microdata']['container_element']]);
        }
        $container->setDefinition(MicrodataRenderer::class, $microdataDefinition);
        $container->setAlias('schema.renderer.microdata', MicrodataRenderer::class);

        // RDFa Renderer
        $rdfaDefinition = new Definition(RdfaRenderer::class);
        $rdfaDefinition->setPublic(false);
        if (isset($config['rdfa'])) {
            $rdfaDefinition->addMethodCall('setPrettyPrint', [$config['rdfa']['pretty_print']]);
            $rdfaDefinition->addMethodCall('setUseSemanticElements', [$config['rdfa']['use_semantic_elements']]);
            $rdfaDefinition->addMethodCall('setIncludeMetaElements', [$config['rdfa']['include_meta_elements']]);
            $rdfaDefinition->addMethodCall('setContainerElement', [$config['rdfa']['container_element']]);
        }
        $container->setDefinition(RdfaRenderer::class, $rdfaDefinition);
        $container->setAlias('schema.renderer.rdfa', RdfaRenderer::class);

        // Default renderer alias
        $defaultRenderer = $config['default_renderer'] ?? 'json_ld';
        $rendererClass = match ($defaultRenderer) {
            'microdata' => MicrodataRenderer::class,
            'rdfa' => RdfaRenderer::class,
            default => JsonLdRenderer::class,
        };
        $container->setAlias(RendererInterface::class, $rendererClass);
        $container->setAlias('schema.renderer', $rendererClass);
    }

    /**
     * Register validation engine.
     *
     * @param ContainerBuilder $container The container builder
     * @param array<mixed>     $config    The processed configuration
     */
    private function registerValidationEngine(ContainerBuilder $container, array $config): void
    {
        $validationDefinition = new Definition(ValidationEngine::class);
        $validationDefinition->setPublic(false);
        $container->setDefinition(ValidationEngine::class, $validationDefinition);
        $container->setAlias('schema.validator', ValidationEngine::class);
    }

    /**
     * Register schema manager.
     *
     * @param ContainerBuilder $container The container builder
     */
    private function registerSchemaManager(ContainerBuilder $container): void
    {
        $managerDefinition = new Definition(SchemaManager::class, [
            new Reference(SchemaFactory::class),
            new Reference(JsonLdRenderer::class),
            new Reference(MicrodataRenderer::class),
            new Reference(RdfaRenderer::class),
            new Reference(ValidationEngine::class),
        ]);
        $managerDefinition->setPublic(true);
        $container->setDefinition(SchemaManager::class, $managerDefinition);
        $container->setAlias('schema.manager', SchemaManager::class);
        $container->setAlias('schema', SchemaManager::class);
    }

    /**
     * Register Twig extension.
     *
     * @param ContainerBuilder $container The container builder
     */
    private function registerTwigExtension(ContainerBuilder $container): void
    {
        $twigExtensionDefinition = new Definition(TwigSchemaExtension::class, [
            new Reference(SchemaManager::class),
        ]);
        $twigExtensionDefinition->addTag('twig.extension');
        $twigExtensionDefinition->setPublic(false);
        $container->setDefinition(TwigSchemaExtension::class, $twigExtensionDefinition);
    }
}
