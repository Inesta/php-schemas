<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Inesta\Schemas\Builder\Factory\SchemaFactory;
use Inesta\Schemas\Core\Performance\SchemaBenchmark;
use Inesta\Schemas\Core\Performance\SchemaCache;

// Demo application showcasing the PHP Schema.org library
echo "# PHP Schema.org Library - Demo Application\n\n";

// 1. Basic Schema Creation
echo "## 1. Basic Schema Creation\n";
$factory = new SchemaFactory();

$article = $factory->create('Article', [
    'headline' => 'Getting Started with Schema.org in PHP',
    'author' => [
        '@type' => 'Person',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ],
    'datePublished' => '2024-01-15T10:30:00+00:00',
    'dateModified' => '2024-01-15T12:00:00+00:00',
    'articleBody' => 'This comprehensive guide shows you how to implement Schema.org structured data in your PHP applications.',
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'Tech Blog',
        'url' => 'https://techblog.example.com',
    ],
    'mainEntityOfPage' => 'https://techblog.example.com/schema-org-php',
    'image' => 'https://techblog.example.com/images/schema-guide.jpg',
]);

echo "✅ Created Article schema with " . count($article->getProperties()) . " properties\n\n";

// 2. Rendering Examples
echo "## 2. Rendering Examples\n";

echo "### JSON-LD Output:\n";
echo "```json\n";
echo $article->toJsonLd();
echo "\n```\n\n";

echo "### Microdata Output:\n";
echo "```html\n";
echo $article->toMicrodata();
echo "\n```\n\n";

echo "### RDFa Output:\n";
echo "```html\n";
echo $article->toRdfa();
echo "\n```\n\n";

// 3. Validation
echo "## 3. Validation\n";
$validationResult = $article->validate();
echo "✅ Validation passed: " . ($validationResult->isValid() ? 'Yes' : 'No') . "\n";
echo "📊 Validation score: " . count($article->getProperties()) . " properties validated\n\n";

// 4. Builder Pattern
echo "## 4. Builder Pattern Example\n";
$person = $factory->create('Person')
    ->withProperty('name', 'Jane Smith')
    ->withProperty('jobTitle', 'Software Engineer')
    ->withProperty('worksFor', [
        '@type' => 'Organization',
        'name' => 'Tech Corp',
        'url' => 'https://techcorp.example.com',
    ])
    ->withProperty('email', 'jane@techcorp.example.com')
    ->withProperty('telephone', '+1-555-0123');

echo "✅ Built Person schema using fluent interface\n";
echo "👤 Person: " . $person->getProperty('name') . " - " . $person->getProperty('jobTitle') . "\n\n";

// 5. Performance Benchmarks
echo "## 5. Performance Benchmarks\n";
echo "Running performance tests...\n\n";

$benchmarks = SchemaBenchmark::runFullBenchmark();

echo "### Creation Performance:\n";
printf("- Simple schemas: %.2f schemas/second\n", $benchmarks['creation']['simple']['schemas_per_second']);
printf("- Complex schemas: %.2f schemas/second\n", $benchmarks['creation']['complex']['schemas_per_second']);

echo "\n### Rendering Performance:\n";
printf("- JSON-LD: %.2f renders/second\n", $benchmarks['rendering']['jsonld']['renders_per_second']);
printf("- Microdata: %.2f renders/second\n", $benchmarks['rendering']['microdata']['renders_per_second']);
printf("- RDFa: %.2f renders/second\n", $benchmarks['rendering']['rdfa']['renders_per_second']);

echo "\n### Cache Performance:\n";
printf("- Cache hit ratio: %.1f%%\n", $benchmarks['cache']['cache_stats']['hit_ratio'] * 100);
printf("- Performance improvement: %.1fx faster with cache\n", $benchmarks['cache']['speedup']);

echo "\n### Memory Usage:\n";
printf("- Peak memory: %.2f MB\n", memory_get_peak_usage(true) / 1024 / 1024);
printf("- Current memory: %.2f MB\n", memory_get_usage(true) / 1024 / 1024);

// 6. Schema Statistics
echo "\n## 6. Library Statistics\n";
$stats = SchemaCache::getStats();
echo "📈 Cache Statistics:\n";
printf("- Cache hits: %d\n", $stats['hits']);
printf("- Cache misses: %d\n", $stats['misses']);
printf("- Cache size: %d items\n", $stats['size']);

echo "\n🎯 Supported Schema Types:\n";
foreach (SchemaFactory::getRegisteredTypes() as $type => $class) {
    echo "- {$type}\n";
}

echo "\n✨ Demo completed successfully!\n";
echo "Visit https://github.com/inesta/php-schemas for documentation and examples.\n";