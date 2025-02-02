#---VARIABLES---------------------------------#
API_DIR = ./api
PHP_CONTAINER = php
#------------#

#---DOCKER---#
DOCKER = docker
DOCKER_RUN = $(DOCKER) run
DOCKER_COMPOSE = docker compose
DOCKER_COMPOSE_UP = $(DOCKER_COMPOSE) up -d
DOCKER_COMPOSE_STOP = $(DOCKER_COMPOSE) stop
DOCKER_COMPOSE_EXEC = $(DOCKER_COMPOSE) exec $(PHP_CONTAINER)
DOCKER_COMPOSE_DOWN = $(DOCKER_COMPOSE) down
#------------#

#---COMPOSER-#
COMPOSER = $(DOCKER_COMPOSE_EXEC) composer
COMPOSER_INSTALL = $(COMPOSER) install -d $(API_DIR)
COMPOSER_UPDATE = $(COMPOSER) update -d $(API_DIR)
#------------#

#---NPM-----#
NPM = $(DOCKER_COMPOSE_EXEC) node npm
NPM_INSTALL = $(NPM) install --force
NPM_UPDATE = $(NPM) update
NPM_BUILD = $(NPM) run build
NPM_DEV = $(NPM) run dev
NPM_WATCH = $(NPM) run watch
#------------#

#---SYMFONY---#
SYMFONY_CONSOLE = $(DOCKER_COMPOSE_EXEC) php bin/console
SYMFONY_LINT = $(SYMFONY_CONSOLE) lint
SYMFONY_PERMISSIONS = $(DOCKER_COMPOSE_EXEC) php chown -R www-data:www-data var
SYMFONY_DD = $(SYMFONY_CONSOLE) doctrine:database:drop --force
SYMFONY_DC = $(SYMFONY_CONSOLE) doctrine:database:create
SYMFONY_DMM = $(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction
SYMFONY_START = $(SYMFONY_CONSOLE) server:start
SYMFONY_STOP = $(SYMFONY_CONSOLE) server:stop
SYMFONY_OPEN = $(SYMFONY_CONSOLE) server:open
#------------#

#---PHPQA---#
PHPQA = jakzal/phpqa
PHPQA_RUN = $(DOCKER_RUN) --init -it --rm -v $(PWD)/api:/app -w /app $(PHPQA)
#------------#

## === 🆘  HELP ==================================================
help: ## Show this help.
	@echo "Symfony-And-Docker-Makefile"
	@echo "---------------------------"
	@echo "Usage: make [target]"
	@echo ""
	@echo "Targets:"
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

#---------------------------------------------#

## === 🐋  DOCKER ================================================
docker-up: ## Start docker containers.
	$(DOCKER_COMPOSE_UP)
.PHONY: docker-up

docker-stop: ## Stop docker containers.
	$(DOCKER_COMPOSE_STOP)
.PHONY: docker-stop

docker-down: ## Stop and remove docker containers.
	$(DOCKER_COMPOSE_DOWN)
.PHONY: docker-down
#---------------------------------------------#

## === 🛠️  SYMFONY =============================================

SYMFONY_MIGRATION_DIFF = $(SYMFONY_CONSOLE) doctrine:migrations:diff
SYMFONY_MIGRATION_MIGRATE = $(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction
SYMFONY_MIGRATION_STATUS = $(SYMFONY_CONSOLE) doctrine:migrations:status
SYMFONY_MIGRATION_UP = $(SYMFONY_CONSOLE) doctrine:migrations:migrate
SYMFONY_MIGRATION_DOWN = $(SYMFONY_CONSOLE) doctrine:migrations:migrate --direction=down

.PHONY: migration-diff migration-migrate migration-status migration-up migration-down symfony-permissions symfony-dd entity

migration-diff: ## Generate a new migration file.
	$(SYMFONY_MIGRATION_DIFF)
.PHONY: migration-diff

migration-migrate: ## Execute the migration.
	$(SYMFONY_MIGRATION_MIGRATE)
.PHONY: migration-migrate

migration-status: ## Show the status of migrations.
	$(SYMFONY_MIGRATION_STATUS)
.PHONY: migration-status

migration-up: ## Migrate up.
	$(SYMFONY_MIGRATION_UP)
.PHONY: migration-up

migration-down: ## Migrate down.
	$(SYMFONY_MIGRATION_DOWN)
.PHONY: migration-down

symfony-permissions: ## Fix permissions.
	$(SYMFONY_PERMISSIONS)
.PHONY: symfony-permissions

symfony-dd: ## Drop database.
	$(SYMFONY_DD)
.PHONY: symfony-dd

entity: ## Create a new entity.
	$(SYMFONY_CONSOLE) make:entity
.PHONY: entity
#---------------------------------------------#

## === 📦  COMPOSER ==============================================
composer-install: ## Install composer dependencies.
	$(COMPOSER_INSTALL)
.PHONY: composer-install

composer-update: ## Update composer dependencies.
	$(COMPOSER_UPDATE)
.PHONY: composer-update

composer-validate: ## Validate composer.json file.
	$(COMPOSER) validate
.PHONY: composer-validate

composer-validate-deep: ## Validate composer.json and composer.lock files in strict mode.
	$(COMPOSER) validate --strict --check-lock
.PHONY: composer-validate-deep
#---------------------------------------------#

## === 📦  NPM ===================================================
npm-install: ## Install npm dependencies.
	$(NPM_INSTALL)
.PHONY: npm-install

npm-update: ## Update npm dependencies.
	$(NPM_UPDATE)
.PHONY: npm-update

npm-build: ## Build assets.
	$(NPM_BUILD)
.PHONY: npm-build

npm-dev: ## Build assets in dev mode.
	$(NPM_DEV)
.PHONY: npm-dev

npm-watch: ## Watch assets.
	$(NPM_WATCH)
.PHONY: npm-watch
#---------------------------------------------#

## === 🐛  PHPQA =================================================
qa-cs-fixer-dry-run: ## Run php-cs-fixer in dry-run mode.
	$(PHPQA_RUN) php-cs-fixer fix /app/src --rules=@Symfony --verbose --dry-run
.PHONY: qa-cs-fixer-dry-run

qa-cs-fixer: ## Run php-cs-fixer.
	$(PHPQA_RUN) php-cs-fixer fix /app/src --rules=@Symfony --verbose
.PHONY: qa-cs-fixer

qa-phpstan: ## Run phpstan.
	$(PHPQA_RUN) phpstan analyse /app/src --level=7
.PHONY: qa-phpstan

qa-phpcpd: ## Run phpcpd (copy/paste detector).
	$(PHPQA_RUN) phpcpd /app/src
.PHONY: qa-phpcpd

qa-php-metrics: ## Run php-metrics.
	$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics /app/src
.PHONY: qa-php-metrics

qa-lint-twigs: ## Lint twig files.
	$(DOCKER_COMPOSE_EXEC) $(PHP_CONTAINER) bin/console lint:twig ./templates
.PHONY: qa-lint-twigs

qa-lint-yaml: ## Lint yaml files.
	$(DOCKER_COMPOSE_EXEC) $(PHP_CONTAINER) bin/console lint:yaml ./config
.PHONY: qa-lint-yaml

qa-lint-container: ## Lint container.
	$(DOCKER_COMPOSE_EXEC) $(PHP_CONTAINER) bin/console lint:container
.PHONY: qa-lint-container

qa-lint-schema: ## Lint Doctrine schema.
	$(SYMFONY_CONSOLE) doctrine:schema:validate --skip-sync -vvv --no-interaction
.PHONY: qa-lint-schema

qa-audit: ## Run composer audit.
	$(COMPOSER) audit
.PHONY: qa-audit
#---------------------------------------------#

## === 🔎  TESTS =================================================
tests: ## Run tests.
	$(DOCKER_COMPOSE_EXEC) php bin/phpunit --testdox
.PHONY: tests

tests-coverage: ## Run tests with coverage.
	$(DOCKER_COMPOSE_EXEC) php bin/phpunit --coverage-html var/coverage
.PHONY: tests-coverage
#---------------------------------------------#

## === ⭐  OTHERS =================================================
before-commit: qa-cs-fixer qa-phpstan qa-phpcpd qa-lint-twigs qa-lint-yaml qa-lint-container qa-lint-schema tests ## Run before commit.
.PHONY: before-commit

first-install: docker-up composer-install npm-install npm-build symfony-permissions symfony-dc symfony-dmm symfony-start symfony-open ## First install.
.PHONY: first-install
