# Installation

## Per project

We recommend adding `swagger-php` to your project using [Composer](https://getcomposer.org)

```shell
> composer require zircote/swagger-php
```

## Globally

Alternatively, use the composer `global` argument to install `swagger-php` globally.

```shell
> composer global require zircote/swagger-php
```
::: warning PATH variables
Remember to add the `~/.composer/vendor/bin` directory to the PATH in your environment.
:::

## Type resolvers

`swagger-php` version `5.5` introduces a new type resolver that is used internally to determine the schema type
of properties (and other elements with a schema).

By default, a custom `LegacyTypeResolver` is used. If you are on PHP 8.2 or higher,
the `TypeInfoTypeResolver` can be used instead.
For this the [radebatz/type-info-extras](https://github.com/DerManoMann/type-info-extras) package is required.

Since it is optional, it needs to be installed manually:

```shell
composer require radebatz/type-info-extras
```

::: warning Additional dependencies
Installing `radebatz/type-info-extras` will also add `symfony/type-info` as a dependency.
:::


## Using doctrine annotations

As of version `4.8` the [doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) library **is optional** and **no longer installed by default**.

If your code uses doctrine annotations you will need to install that library manually:

```shell
composer require doctrine/annotations
```
