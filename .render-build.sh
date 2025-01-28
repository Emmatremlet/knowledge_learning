#!/usr/bin/env bash
# Set PHP version
echo "Setting up PHP 8.2"
export PHP_VERSION=8.2
composer install --no-dev --optimize-autoloader