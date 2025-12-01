# Netgen Content Browser

## Installation instructions

### Use Composer

Run the following command to install Netgen Content Browser:

```bash
composer require netgen/content-browser
```

Symfony Flex will automatically enable the bundle and import the routes.

### Install assets

Run the following from your repo root to install Content Browser assets:

```bash
php bin/console assets:install --symlink --relative
```

## Running tests

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

You can run unit tests by calling `composer test` from the repo root:

```bash
$ composer test
```

## Running API tests

You can run API tests by calling `composer test-api` from the repo root:

```bash
$ composer test-api
```
