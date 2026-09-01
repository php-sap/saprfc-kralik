SHELL := /bin/sh
.DEFAULT_GOAL := help

COMPOSER_IMAGE     := composer:2
COMPOSER_CACHE_DIR := $(HOME)/.cache/composer
DOCKER_USER        := --user "$$(id -u)":"$$(id -g)"
DOCKER_MOUNT       := --volume "$$(pwd)":/app --workdir /app

# --prefer-lowest only has an effect on "composer update", not "composer
# install" (which just reproduces composer.lock) - switch commands so the
# flag actually does something, matching the CI "lowest" matrix job.
ifdef DEPENDENCIES_LOWEST
COMPOSER_INSTALL_CMD := update --prefer-lowest
else
COMPOSER_INSTALL_CMD := install
endif

# Optional: set CA_CERT_FILE to a PEM file (e.g. a corporate proxy root CA)
# to make it trusted for HTTPS network access inside the containers used by
# "install" and "audit" (e.g. 'make install PHP_VERSION=8.1 CA_CERT_FILE=/path/to/ca.pem').
ifdef CA_CERT_FILE
CA_MOUNT     := --volume "$(CA_CERT_FILE)":/tmp/extra-ca.crt:ro
CA_TRUST_CMD := cat /etc/ssl/certs/ca-certificates.crt /tmp/extra-ca.crt > /tmp/ca-bundle.pem && export CURL_CA_BUNDLE=/tmp/ca-bundle.pem SSL_CERT_FILE=/tmp/ca-bundle.pem &&
else
CA_MOUNT     :=
CA_TRUST_CMD :=
endif

.PHONY: help clean check-php-version install test lint analyze beautify sniff audit validate

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

clean: ## Remove vendor/ composer.lock and .phpunit.result.cache (reset to a fresh checkout)
	rm -rf vendor composer.lock .phpunit.result.cache

check-php-version:
	@if [ -z "$(PHP_VERSION)" ]; then \
		echo "Error: PHP_VERSION must be set, e.g. 'make $(MAKECMDGOALS) PHP_VERSION=8.1'." >&2; \
		exit 1; \
	fi

install: check-php-version ## Install composer dependencies for PHP_VERSION (set DEPENDENCIES_LOWEST for --prefer-lowest, CA_CERT_FILE for a corporate proxy CA)
	@mkdir -p "$(COMPOSER_CACHE_DIR)"
	docker run --rm -t --init $(DOCKER_USER) \
		--env HTTP_PROXY --env HTTPS_PROXY --env NO_PROXY \
		--env COMPOSER_CACHE_DIR=/tmp/composer-cache \
		--volume "$(COMPOSER_CACHE_DIR)":/tmp/composer-cache \
		$(CA_MOUNT) \
		$(DOCKER_MOUNT) \
		$(COMPOSER_IMAGE) sh -c '\
			$(CA_TRUST_CMD) \
			composer config platform.php "$(PHP_VERSION)" && \
			composer $(COMPOSER_INSTALL_CMD) --prefer-dist --no-interaction --no-progress --ignore-platform-req=ext-sapnwrfc; \
			status=$$?; \
			composer config --unset platform.php; \
			composer config --unset platform 2>/dev/null; \
			composer config --unset config 2>/dev/null; \
			if [ $$status -eq 0 ]; then \
				composer update --lock --no-interaction --no-progress; \
				status=$$?; \
			fi; \
			exit $$status \
		'

test: check-php-version ## Run PHPUnit for PHP_VERSION
	docker run --rm -t --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		"php:$(PHP_VERSION)-cli" php vendor/bin/phpunit

lint: check-php-version ## Syntax-check every .php file in src/ and tests/ for PHP_VERSION
	docker run --rm --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		"php:$(PHP_VERSION)-cli" sh -c "find src tests -type f -name '*.php' -print0 | xargs -0 -n1 php -l"

analyze: check-php-version ## Run PHPStan for PHP_VERSION
	docker run --rm -t --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		"php:$(PHP_VERSION)-cli" php vendor/bin/phpstan analyse --memory-limit=-1

beautify: check-php-version ## Run PHPCBF (auto-fix code style) for PHP_VERSION
	docker run --rm --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		"php:$(PHP_VERSION)-cli" php vendor/bin/phpcbf

sniff: check-php-version ## Run PHPCS (code style check) for PHP_VERSION
	docker run --rm --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		"php:$(PHP_VERSION)-cli" php vendor/bin/phpcs

audit: ## Run composer audit (checks dependencies for known vulnerabilities; CA_CERT_FILE for a corporate proxy CA)
	docker run --rm --init $(DOCKER_USER) \
		--env HTTP_PROXY --env HTTPS_PROXY --env NO_PROXY \
		$(CA_MOUNT) $(DOCKER_MOUNT) \
		$(COMPOSER_IMAGE) sh -c '$(CA_TRUST_CMD) composer audit'

validate: ## Run composer validate --strict
	docker run --rm --init $(DOCKER_USER) $(DOCKER_MOUNT) \
		$(COMPOSER_IMAGE) composer validate --strict
