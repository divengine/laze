# Div PHP Laze

**laze** is a PHP library for defining lazy constants. Values are set as closures and only materialize upon first access, ensuring efficient and controlled initialization. Once a value is evaluated, it becomes immutable and cannot be redefined as a value, though it can be redefined as a closure until it's accessed.

## Installation

You can install **laze** using Composer:

```bash
composer require divengine/laze
```

Or add it to your composer.json file:

```json
{
    "require": {
        "divengine/laze": "^1.0.0"
    }
}
```

## Usage

### Defining a Lazy Constant

To define a lazy constant, use the `laze::define` method. The value must be provided as a closure.

```php

use divengine\laze;

// Define a lazy constant
laze::define('MY_CONSTANT', function() {
    return computeExpensiveValue();
});
```

### Reading a Lazy Constant

To access the value, use the Laze::read method. The closure will be evaluated on the first access, and the result will be stored as the constant's value.

```php
$value = laze::read('MY_CONSTANT');
```

## Example

```php
use divengine\laze;

// Define a lazy constant
laze::define('MY_CONSTANT', function() {
    return rand(1, 100); // Simulate an expensive computation
});

// First access, the closure is evaluated
$value = laze::read('MY_CONSTANT');
echo $value; // Outputs the evaluated value

// Subsequent access returns the stored value
$value = laze::read('MY_CONSTANT');
echo $value; // Outputs the same value as before
```

## License

This project is licensed under the GNU GENERAL PUBLIC LICENSE. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue.

## About

`laze` is developed and maintained by [Divengine Software Solutions](https://divengine.com). If you find this library useful, please consider starring the repository and sharing it with others.
