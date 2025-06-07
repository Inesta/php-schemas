<?php

declare(strict_types=1);

namespace Inesta\Schemas\Adapters\Laravel;

use Blade;
use Illuminate\Support\ServiceProvider;
use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Contracts\RendererInterface;
use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer;
use Inesta\Schemas\Renderer\Microdata\MicrodataRenderer;
use Inesta\Schemas\Renderer\Rdfa\RdfaRenderer;
use Inesta\Schemas\Validation\ValidationEngine;

use function config_path;

/**
 * Laravel service provider for Schema.org library.
 *
 * Registers the schema factory, renderers, and validation engine
 * with Laravel's service container for easy dependency injection.
 */
final class SchemaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the schema factory as a singleton
        $this->app->singleton(SchemaFactory::class, static fn (): SchemaFactory => new SchemaFactory());

        // Register renderers as singletons
        $this->app->singleton('schema.renderer.json-ld', static fn (): JsonLdRenderer => new JsonLdRenderer());

        $this->app->singleton('schema.renderer.microdata', static fn (): MicrodataRenderer => new MicrodataRenderer());

        $this->app->singleton('schema.renderer.rdfa', static fn (): RdfaRenderer => new RdfaRenderer());

        // Register default renderer (JSON-LD)
        $this->app->singleton(RendererInterface::class, static fn ($app): JsonLdRenderer => $app->make('schema.renderer.json-ld'));

        // Register validation engine
        $this->app->singleton(ValidationEngine::class, static fn (): ValidationEngine => new ValidationEngine());

        // Register the schema manager
        $this->app->singleton(SchemaManager::class, static fn ($app): SchemaManager => new SchemaManager(
            $app->make(SchemaFactory::class),
            $app->make('schema.renderer.json-ld'),
            $app->make('schema.renderer.microdata'),
            $app->make('schema.renderer.rdfa'),
            $app->make(ValidationEngine::class),
        ));

        // Register facade alias
        $this->app->alias(SchemaManager::class, 'schema');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/schema.php' => config_path('schema.php'),
            ], 'schema-config');
        }

        // Load configuration
        $this->mergeConfigFrom(__DIR__ . '/config/schema.php', 'schema');

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            SchemaFactory::class,
            'schema.renderer.json-ld',
            'schema.renderer.microdata',
            'schema.renderer.rdfa',
            RendererInterface::class,
            ValidationEngine::class,
            SchemaManager::class,
            'schema',
        ];
    }

    /**
     * Register Blade directives for schema rendering.
     */
    private function registerBladeDirectives(): void
    {
        // @schema directive for inline schema rendering
        Blade::directive('schema', static fn (string $expression): string => "<?php echo app('schema')->render({$expression}); ?>");

        // @jsonld directive for JSON-LD rendering
        Blade::directive('jsonld', static fn (string $expression): string => "<?php echo app('schema')->renderJsonLd({$expression}); ?>");

        // @microdata directive for Microdata rendering
        Blade::directive('microdata', static fn (string $expression): string => "<?php echo app('schema')->renderMicrodata({$expression}); ?>");

        // @rdfa directive for RDFa rendering
        Blade::directive('rdfa', static fn (string $expression): string => "<?php echo app('schema')->renderRdfa({$expression}); ?>");
    }
}
