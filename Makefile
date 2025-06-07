.PHONY: help install update test coverage cs-check cs-fix analyse psalm check-all clean

# Default target
.DEFAULT_GOAL := help

# Colors for output
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
CYAN   := $(shell tput -Txterm setaf 6)
RESET  := $(shell tput -Txterm sgr0)

help: ## Show this help message
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} { \
		if (/^[a-zA-Z_-]+:.*?##.*$$/) {printf "  ${YELLOW}%-20s${GREEN}%s${RESET}\n", $$1, $$2} \
		else if (/^## .*$$/) {printf "  ${CYAN}%s${RESET}\n", substr($$1,4)} \
		}' $(MAKEFILE_LIST)

## Development

install: ## Install dependencies
	@echo "${GREEN}Installing dependencies...${RESET}"
	@composer install --no-interaction --prefer-dist --optimize-autoloader
	@echo "${GREEN}Dependencies installed!${RESET}"

update: ## Update dependencies
	@echo "${GREEN}Updating dependencies...${RESET}"
	@composer update --no-interaction --prefer-dist --optimize-autoloader
	@echo "${GREEN}Dependencies updated!${RESET}"

## Testing

test: ## Run all tests
	@echo "${GREEN}Running tests...${RESET}"
	@vendor/bin/phpunit

test-unit: ## Run unit tests
	@echo "${GREEN}Running unit tests...${RESET}"
	@vendor/bin/phpunit --testsuite unit

test-integration: ## Run integration tests
	@echo "${GREEN}Running integration tests...${RESET}"
	@vendor/bin/phpunit --testsuite integration

test-compliance: ## Run compliance tests
	@echo "${GREEN}Running compliance tests...${RESET}"
	@vendor/bin/phpunit --testsuite compliance

coverage: ## Generate code coverage report
	@echo "${GREEN}Generating coverage report...${RESET}"
	@vendor/bin/phpunit --coverage-html build/coverage --coverage-text
	@echo "${GREEN}Coverage report generated in build/coverage/index.html${RESET}"

infection: ## Run mutation testing
	@echo "${GREEN}Running mutation tests...${RESET}"
	@vendor/bin/infection --threads=4

## Code Quality

cs-check: ## Check code style
	@echo "${GREEN}Checking code style...${RESET}"
	@vendor/bin/php-cs-fixer fix --dry-run --diff

cs-fix: ## Fix code style
	@echo "${GREEN}Fixing code style...${RESET}"
	@vendor/bin/php-cs-fixer fix

analyse: ## Run PHPStan analysis
	@echo "${GREEN}Running PHPStan analysis...${RESET}"
	@vendor/bin/phpstan analyse --memory-limit=2G

psalm: ## Run Psalm analysis
	@echo "${GREEN}Running Psalm analysis...${RESET}"
	@vendor/bin/psalm

check-all: ## Run all checks (CS, PHPStan, Psalm, Tests)
	@echo "${GREEN}Running all checks...${RESET}"
	@$(MAKE) cs-check
	@$(MAKE) analyse
	@$(MAKE) psalm
	@$(MAKE) test
	@echo "${GREEN}All checks passed!${RESET}"

## Validation

validate-schema: ## Validate schemas against Schema.org
	@echo "${GREEN}Validating schemas...${RESET}"
	@php bin/validate-schema.php

validate-examples: ## Validate all example files
	@echo "${GREEN}Validating examples...${RESET}"
	@php bin/validate-examples.php

## Documentation

docs: ## Generate API documentation
	@echo "${GREEN}Generating documentation...${RESET}"
	@php bin/generate-docs.php

## Utilities

clean: ## Clean build artifacts
	@echo "${GREEN}Cleaning build artifacts...${RESET}"
	@rm -rf build/
	@rm -rf .phpunit.cache/
	@rm -rf .phpunit.result.cache
	@rm -rf .php-cs-fixer.cache
	@rm -rf .phpstan/
	@rm -rf .infection/
	@echo "${GREEN}Build artifacts cleaned!${RESET}"

docker-build: ## Build Docker development environment
	@echo "${GREEN}Building Docker environment...${RESET}"
	@docker-compose build

docker-up: ## Start Docker development environment
	@echo "${GREEN}Starting Docker environment...${RESET}"
	@docker-compose up -d

docker-down: ## Stop Docker development environment
	@echo "${GREEN}Stopping Docker environment...${RESET}"
	@docker-compose down

docker-shell: ## Open shell in Docker container
	@docker-compose exec app sh