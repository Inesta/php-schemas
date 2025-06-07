<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Schema Context
    |--------------------------------------------------------------------------
    |
    | The default context URL to use for Schema.org markup. This should
    | typically remain as the official Schema.org context unless you
    | have specific requirements for custom vocabularies.
    |
    */
    'context' => 'https://schema.org',

    /*
    |--------------------------------------------------------------------------
    | Default Renderer
    |--------------------------------------------------------------------------
    |
    | The default renderer to use when calling the generic render() method.
    | Available options: 'json-ld', 'microdata', 'rdfa'
    |
    */
    'default_renderer' => 'json-ld',

    /*
    |--------------------------------------------------------------------------
    | JSON-LD Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the JSON-LD renderer.
    |
    */
    'json_ld' => [
        'pretty_print' => \env('SCHEMA_JSON_LD_PRETTY_PRINT', true),
        'include_script_tag' => \env('SCHEMA_JSON_LD_SCRIPT_TAG', true),
        'unescape_slashes' => \env('SCHEMA_JSON_LD_UNESCAPE_SLASHES', true),
        'unescape_unicode' => \env('SCHEMA_JSON_LD_UNESCAPE_UNICODE', true),
        'compact_output' => \env('SCHEMA_JSON_LD_COMPACT', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Microdata Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Microdata renderer.
    |
    */
    'microdata' => [
        'pretty_print' => \env('SCHEMA_MICRODATA_PRETTY_PRINT', true),
        'use_semantic_elements' => \env('SCHEMA_MICRODATA_SEMANTIC', true),
        'include_meta_elements' => \env('SCHEMA_MICRODATA_META', true),
        'container_element' => \env('SCHEMA_MICRODATA_CONTAINER', 'div'),
    ],

    /*
    |--------------------------------------------------------------------------
    | RDFa Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the RDFa renderer.
    |
    */
    'rdfa' => [
        'pretty_print' => \env('SCHEMA_RDFA_PRETTY_PRINT', true),
        'use_semantic_elements' => \env('SCHEMA_RDFA_SEMANTIC', true),
        'include_meta_elements' => \env('SCHEMA_RDFA_META', true),
        'container_element' => \env('SCHEMA_RDFA_CONTAINER', 'div'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for schema validation.
    |
    */
    'validation' => [
        'enabled' => \env('SCHEMA_VALIDATION_ENABLED', true),
        'strict_mode' => \env('SCHEMA_VALIDATION_STRICT', false),
        'rules' => [
            'required_properties' => true,
            'property_types' => true,
            'empty_values' => true,
            'schema_org_compliance' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching rendered schemas to improve performance.
    |
    */
    'cache' => [
        'enabled' => \env('SCHEMA_CACHE_ENABLED', false),
        'ttl' => \env('SCHEMA_CACHE_TTL', 3600), // 1 hour
        'prefix' => \env('SCHEMA_CACHE_PREFIX', 'schema:'),
        'store' => \env('SCHEMA_CACHE_STORE', null), // Use default cache store
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for debugging and development features.
    |
    */
    'debug' => [
        'enabled' => \env('SCHEMA_DEBUG', \env('APP_DEBUG', false)),
        'log_validation_errors' => \env('SCHEMA_LOG_VALIDATION', false),
        'log_rendering_time' => \env('SCHEMA_LOG_TIMING', false),
    ],
];
