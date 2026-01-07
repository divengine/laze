# Div PHP Laze

[![Latest Stable Version](https://poser.pugx.org/divengine/laze/v)](https://packagist.org/packages/divengine/laze) [![Total Downloads](https://poser.pugx.org/divengine/laze/downloads)](https://packagist.org/packages/divengine/laze) [![Latest Unstable Version](https://poser.pugx.org/divengine/laze/v/unstable)](https://packagist.org/packages/divengine/laze) [![License](https://poser.pugx.org/divengine/laze/license)](https://packagist.org/packages/divengine/laze) [![PHP Version Require](https://poser.pugx.org/divengine/laze/require/php)](https://packagist.org/packages/divengine/laze)

Laze provides lazy immutable values for PHP. You define a key with a closure,
and the value is computed on first read and then cached.

## Requirements

- PHP 8.0 or higher

## Installation

```shell
composer require divengine/laze
```

## Quick start

```php
<?php

require 'vendor/autoload.php';

use divengine\laze;

laze::define('APP_NAME', fn () => 'Laze Demo');

echo laze::read('APP_NAME');
```

## Core API

- `laze::define($key, $callable)` registers a lazy value.
- `laze::read($key)` evaluates once and returns the stored value.
- `laze::defined($key)` checks if a key exists.
- `laze::evaluated($key)` checks if a key was evaluated (throws if undefined).
- `laze::constraint($name, $checker)` validates values on evaluation.

## Redefinition rules

You can redefine a value before it is evaluated. After the first `read`, the
value becomes immutable and further `define` calls are ignored.

```php
<?php

laze::define('FOO', fn () => 1);
laze::define('FOO', fn () => 2);

echo laze::read('FOO'); // 2

laze::define('FOO', fn () => 3);
echo laze::read('FOO'); // still 2
```

## Constraints

```php
<?php

laze::constraint(
    'APP_PORT must be an int',
    fn ($key, $value) => $key === 'APP_PORT' ? is_int($value) : true
);

laze::define('APP_PORT', fn () => 8080);
```

Constraints run when a value is evaluated. If a constraint returns `false`,
`read` throws an exception.

## Testing tips

Laze stores state statically. You can reset it between tests with reflection:

```php
<?php

use divengine\laze;

$reflection = new ReflectionClass(laze::class);

$store = $reflection->getProperty('store');
$store->setAccessible(true);
$store->setValue(null, []);

$constraints = $reflection->getProperty('constraints');
$constraints->setAccessible(true);
$constraints->setValue(null, []);

$evaluated = $reflection->getProperty('evaluated');
$evaluated->setAccessible(true);
$evaluated->setValue(null, []);
```

Or run PHPUnit with process isolation:

```shell
vendor/bin/phpunit --process-isolation
```

## Docs

See `docs/README.md` for guides and FAQ.

## License

This project is licensed under the GNU General Public License. See `LICENSE`.

## Contributing

Contributions are welcome. Please open an issue or submit a pull request.

## About

Laze is developed and maintained by [Divengine Software Solutions](https://divengine.com).
