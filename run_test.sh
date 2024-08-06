#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

# Create the test database
php bin/console doctrine:database:create --env=test

# Run migrations to set up the schema
php bin/console doctrine:s:u --env=test -f

# Load the fixtures
php bin/console doctrine:fixtures:load --env=test --group=AppFixtures --no-interaction
php bin/console doctrine:fixtures:load --env=test --group=TestFixtures --no-interaction

# Run the tests
./vendor/bin/phpunit