# Laze Docs

Laze provides lazy immutable values for PHP. You define a key with a closure,
and the value is computed on first read and then cached.

## Quick links

- [Installation](Installation.md)
- [Div PHP Laze](Div%20PHP%20Laze.md)
- [Best practices](Best%20practices.md)
- [FAQ](FAQ.md)

## Quick start

```php
<?php

require 'vendor/autoload.php';

use divengine\laze;

laze::define('CONFIG', fn () => parse_ini_file('app.ini'));

$config = laze::read('CONFIG');
```

## Related resources

- Tests: `../tests/BasicTest.php`
- Source: `../src/laze.php`
