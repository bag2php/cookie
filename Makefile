# Makefile for PHP Project
#
# Copyright (c) 2019  USAMI Kenta
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.
#
# License: MIT

PHP ?= php
PHPDBG ?= phpdbg -qrr
COMPOSER = composer
AUTOLOAD_PHP = vendor/autoload.php
RM = rm -f
APP_ENV =
PHPDOCUMENTOR_PHAR_URL = https://github.com/phpDocumentor/phpDocumentor/releases/download/v3.0.0-rc/phpDocumentor.phar

.DEFAULT_GOAL := check

composer.lock: composer.json
	$(COMPOSER) install --no-progress

$(AUTOLOAD_PHP): composer.lock

tools/.infection/vendor/bin/infection:
	$(COMPOSER) install --no-progress -d tools/.infection

tools/.phan/vendor/bin/phan:
	$(COMPOSER) install --no-progress -d tools/.phan

tools/.php-cs-fixer/vendor/bin/php-cs-fixer:
	$(COMPOSER) install --no-progress -d tools/.php-cs-fixer

tools/.phpDocumentor.phar:
	$(PHP) -r "copy('$(PHPDOCUMENTOR_PHAR_URL)', 'tools/.phpDocumentor.phar');"
	+chmod +x tools/.phpDocumentor.phar

tools/.phpstan/vendor/bin/phpstan:
	$(COMPOSER) install --no-progress -d tools/.phpstan

tools/.psalm/vendor/bin/psalm:
	$(COMPOSER) install --no-progress -d tools/.psalm

tools/infection: tools/.infection/vendor/bin/infection
	(cd tools; ln -sf .infection/vendor/bin/infection .)

tools/phan: tools/.phan/vendor/bin/phan
	(cd tools; ln -sf .phan/vendor/bin/phan .)

tools/php-cs-fixer: tools/.php-cs-fixer/vendor/bin/php-cs-fixer
	(cd tools; ln -sf .php-cs-fixer/vendor/bin/php-cs-fixer .)

tools/phpdoc: tools/phpDocumentor.phar
	(cd tools; ln -sf .phpDocumentor.phar .)

tools/phpstan: tools/.phpstan/vendor/bin/phpstan
	(cd tools; ln -sf .phpstan/vendor/bin/phpstan .)

tools/psalm: tools/.psalm/vendor/bin/psalm
	(cd tools; ln -sf .psalm/vendor/bin/psalm .)

.PHONY: analyse analyse-no-dev check composer composer-no-dev clean clobber doc fix phan \
phan-strict phpdoc phpstan-no-dev phpstan psalm psalm-strict setup setup-tools test

analyse-no-dev: phan phpstan-no-dev psalm-no-dev
analyse-strict: phan-strict phpstan-strict psalm-strict
analyse: phan phpstan-use-baseline psalm
check: composer test analyse

check-strict: composer test infection analyse-strict

composer: $(AUTOLOAD_PHP)

composer-no-dev:
	$(COMPOSER) install --no-dev --optimize-autoloader --no-progress

clobber: clean
	-$(RM) tools/*.phar tools/phan tools/php-cs-fixer tools/phpstan tools/psalm
	-$(RM) -r tools/.infection/composer.lock tools/.infection/vendor
	-$(RM) -r tools/.phan/composer.lock tools/.phan/vendor
	-$(RM) -r tools/.php-cs-fixer/composer.lock tools/.php-cs-fixer/vendor
	-$(RM) -r tools/.phpdocumentor/phpDocumentor.phar
	-$(RM) -r tools/.phpstan/composer.lock tools/.phpstan/vendor
	-$(RM) -r tools/.psalm/composer.lock tools/.psalm/vendor
	-$(RM) -r vendor
	-$(RM) composer.lock

clean:
	-$(RM) -r build
	-$(RM) .php_cs.cache
	-$(RM) infection.log

doc: phpdoc

fix: tools/php-cs-fixer
	$(PHP) tools/php-cs-fixer fix

infection: tools/infection
	$(PHPDBG) tools/infection

phan: tools/phan
	$(PHP) tools/phan --analyze-twice --no-progress-bar 2>/dev/null
	@echo

phan-strict: tools/phan
	$(PHP) tools/phan --analyze-twice --no-progress-bar -tux 2>/dev/null
	@echo

phpdoc: tools/phpdoc
	APP_ENV=$(APP_ENV) $(PHP) tools/phpdoc

phpstan-no-dev: tools/phpstan
	$(PHP) tools/phpstan analyse --no-progress src/

phpstan-use-baseline: tools/phpstan
	$(PHP) tools/phpstan analyse --no-progress -c phpstan-baseline.neon.dist

phpstan: tools/phpstan
	$(PHP) tools/phpstan analyse --no-progress

psalm-no-dev: tools/psalm
	$(PHP) tools/psalm --no-progress src/

psalm: tools/psalm
	$(PHP) tools/psalm --no-progress

psalm-strict: tools/psalm
	$(PHP) tools/psalm --no-progress --show-info=true

setup: $(COMPOSER)

setup-doc: setup tools/phpdoc

setup-tools: setup tools/php-cs-fixer tools/phan tools/phpstan tools/psalm

test: vendor/bin/phpunit
	$(PHP) vendor/bin/phpunit
	@echo
