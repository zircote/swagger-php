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

## Type resolver

`swagger-php` determines the schema type of properties (and other elements with a schema) with
[symfony/type-info](https://github.com/symfony/type-info), through
[radebatz/type-info-extras](https://github.com/DerManoMann/type-info-extras). Both are installed with
`swagger-php`.


## Using doctrine annotations <Badge type="warning" text="deprecated" />

As of version `4.8` the [doctrine annotations](https://www.doctrine-project.org/projects/annotations.html) library **is optional** and **no longer installed by default**.

If your code uses doctrine annotations, you will need to install that library manually:

```shell
composer require doctrine/annotations
```
