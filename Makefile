# PHP Schema.org Library - Makefile
# Provides convenient commands for development tasks

.PHONY: help install test analyze fix clean docker-build docker-test docs

# Default target
help: ## Show this help message
	@echo "PHP Schema.org Library - Development Commands"
	@echo "============================================="
	@echo ""
	@echo "Available commands:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Installation and setup
install: ## Install dependencies
	composer install

install-dev: ## Install development dependencies
	composer install --dev

update: ## Update dependencies
	composer update

# Testing
test: ## Run all tests
	composer test

test-unit: ## Run unit tests only
	composer test:unit

test-integration: ## Run integration tests only
	composer test:integration

test-compliance: ## Run compliance tests only
	composer test:compliance

test-coverage: ## Run tests with coverage report
	composer test:coverage

# Code quality
analyze: ## Run static analysis (PHPStan)
	composer analyse

psalm: ## Run Psalm static analysis
	composer psalm

cs-check: ## Check code style
	composer cs:check

cs-fix: ## Fix code style issues
	composer cs:fix

metrics: ## Generate code metrics
	composer metrics

# Validation
validate: ## Validate composer.json and schemas
	composer validate
	composer validate:schema

validate-examples: ## Validate all examples
	composer validate:examples

# All quality checks
check-all: ## Run all quality checks
	composer check-all

# Documentation
docs: ## Generate documentation
	composer docs:generate

docs-serve: ## Serve documentation locally (requires docsify)
	@if command -v docsify >/dev/null 2>&1; then \
		cd docs && docsify serve .; \
	else \
		echo "docsify not found. Install with: npm install -g docsify-cli"; \
	fi

# Docker commands
docker-build: ## Build Docker development environment
	docker-compose build

docker-up: ## Start Docker development environment
	docker-compose up -d

docker-down: ## Stop Docker development environment
	docker-compose down

docker-shell: ## Open shell in PHP container
	docker-compose exec php bash

docker-test: ## Run tests in Docker container
	docker-compose exec php composer test

docker-analyze: ## Run analysis in Docker container
	docker-compose exec php composer analyse

docker-clean: ## Clean Docker containers and images
	docker-compose down -v --rmi all

# Development utilities
clean: ## Clean cache and temporary files
	rm -rf vendor/
	rm -rf .php-cs-fixer.cache
	rm -rf .phpunit.result.cache
	composer clear-cache

reset: clean install ## Reset project (clean + install)

demo: ## Run demo examples
	@echo "Running JSON-LD example:"
	@php -r "require 'vendor/autoload.php'; \
		use Inesta\Schemas\Builder\Builders\ArticleBuilder; \
		use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer; \
		\$$article = (new ArticleBuilder()) \
			->headline('Demo Article') \
			->author('Demo Author') \
			->datePublished(new DateTime()) \
			->build(); \
		\$$renderer = new JsonLdRenderer(); \
		\$$renderer->setPrettyPrint(true)->setIncludeScriptTag(true); \
		echo \$$renderer->render(\$$article);"

benchmark: ## Run performance benchmarks
	@echo "Running benchmarks..."
	@php -d memory_limit=512M -r "require 'vendor/autoload.php'; \
		use Inesta\Schemas\Builder\Builders\ArticleBuilder; \
		use Inesta\Schemas\Renderer\JsonLd\JsonLdRenderer; \
		\$$start = microtime(true); \
		for (\$$i = 0; \$$i < 1000; \$$i++) { \
			\$$article = (new ArticleBuilder()) \
				->headline('Benchmark Article ' . \$$i) \
				->author('Benchmark Author') \
				->datePublished(new DateTime()) \
				->build(); \
			\$$renderer = new JsonLdRenderer(); \
			\$$renderer->render(\$$article); \
		} \
		\$$end = microtime(true); \
		\$$time = round((\$$end - \$$start) * 1000, 2); \
		\$$memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2); \
		echo \"Created and rendered 1000 schemas in \$$time ms\n\"; \
		echo \"Peak memory usage: \$$memory MB\n\";"

# Git hooks
hooks-install: ## Install git hooks
	vendor/bin/captainhook install

hooks-update: ## Update git hooks
	vendor/bin/captainhook configure

# CI/CD helpers
ci-setup: ## Setup for CI environment
	composer install --no-dev --optimize-autoloader

ci-test: ## Run CI test suite
	composer test
	composer analyse
	composer cs:check

# Development server for examples
serve-examples: ## Serve examples directory (requires PHP built-in server)
	@echo "Starting development server at http://localhost:8000"
	@echo "Serving examples from docs/ directory"
	@cd docs && php -S localhost:8000

# Schema validation
validate-google: ## Validate schemas with Google's Rich Results Test (requires internet)
	@echo "Note: Manual validation required at https://search.google.com/test/rich-results"
	@echo "Copy the JSON-LD output from examples and test it manually"

# Release helpers
tag-version: ## Tag a new version (usage: make tag-version VERSION=1.0.0)
	@if [ -z "$(VERSION)" ]; then \
		echo "Usage: make tag-version VERSION=1.0.0"; \
		exit 1; \
	fi
	@echo "Tagging version $(VERSION)"
	git tag -a v$(VERSION) -m "Release v$(VERSION)"
	git push origin v$(VERSION)

# Security
security-check: ## Run security analysis
	composer audit

# Help for specific environments
help-laravel: ## Show Laravel integration help
	@echo "Laravel Integration:"
	@echo "  1. Install: composer require inesta/php-schemas"
	@echo "  2. Publish config: php artisan vendor:publish --tag=schema-config"
	@echo "  3. Use facade: Schema::article(['headline' => 'Title'])"

help-symfony: ## Show Symfony integration help
	@echo "Symfony Integration:"
	@echo "  1. Install: composer require inesta/php-schemas"
	@echo "  2. Add bundle to config/bundles.php"
	@echo "  3. Configure in config/packages/schema.yaml"
	@echo "  4. Use in Twig: {{ schema_article({'headline': 'Title'})|json_ld|raw }}"

# Development workflow
dev-workflow: ## Run complete development workflow
	@echo "Running complete development workflow..."
	make clean
	make install
	make test
	make analyze
	make cs-check
	@echo "✅ Development workflow completed successfully!"

# Production build
build-prod: ## Build for production
	composer install --no-dev --optimize-autoloader
	composer dump-autoload --optimize --no-dev

# IDE helpers
ide-helper: ## Generate IDE helper files
	@echo "IDE helper file already exists at .ide-helper.php"
	@echo "Include this file in your IDE for better autocomplete support"