# Div PHP Laze

Laze is a PHP library for lazy immutable values. You define a key with a closure,
and the closure is evaluated on first read. After evaluation, the value is
stored and cannot be replaced.

## Core ideas

- Lazy evaluation: compute values only when needed.
- Immutability: after evaluation, values stay fixed.
- Constraints: validate values when they materialize.

## Basic usage

```php
<?php

require 'vendor/autoload.php';

use divengine\laze;

laze::define('APP_NAME', fn () => 'Laze Demo');

$name = laze::read('APP_NAME');
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

## Redefinition rules

You can redefine a value before it is evaluated. After the first `read`, the
value becomes immutable and further `define` calls are ignored.

## Related docs

- [Installation](Installation.md)
- [Best practices](Best%20practices.md)
- [FAQ](FAQ.md)
