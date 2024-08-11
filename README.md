# Div PHP Laze

**laze** is a PHP library designed for defining _lazy evaluation_. Values are set as closures and only materialize upon first access, ensuring efficient and controlled initialization. Once a value is evaluated, it becomes immutable and cannot be redefined as a different value. However, it can be redefined as a `closure` until it’s accessed, at which point it transforms into a non-closure value.

**laze** might be an English word that suggests relaxation or laziness, but in this context, it’s actually an acronym derived from **Lazy Evaluation**. This refers to a programming technique where the evaluation of an expression is delayed until its value is needed. With **laze**, once the value is evaluated, it **becomes an immutable value**. In other words, a value that, although evaluated with delay, cannot be modified after its initial evaluation. Thus, **laze** encapsulates the concept of deferred evaluation that results in a definitive value, combining flexibility and robustness into one concept.

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
laze::define('MY_CONSTANT', fn() => computeExpensiveValue());
```

### Reading a Lazy Constant

To access the value, use the Laze::read method. The closure will be evaluated on the first access, and the result will be stored as the constant's value.

```php
$value = laze::read('MY_CONSTANT');
```

## Basic example

```php
use divengine\laze;

// Define a lazy constant. Simulate an expensive computation
laze::define('MY_CONSTANT', fn() => return rand(1, 100)); 

// First access, the closure is evaluated
$value = laze::read('MY_CONSTANT');
echo $value; // Outputs the evaluated value

// Subsequent access returns the stored value
$value = laze::read('MY_CONSTANT');
echo $value; // Outputs the same value as before
```

## Using `laze` with PHPUnit

`laze` can be particularly useful in testing environments where you need to redefine values differently from their production values. This example demonstrates how to define a lazy value in a standard PHP file and then override it during unit tests.

### 1. Define a lazy constant in `index.php`

In your `index.php` file, define a lazy constant using `laze::define` and create a function that uses this constant.

```php
use divengine\laze;

require_once 'vendor/autoload.php';

// Define a lazy constant
laze::define('GREETING', fn() => 'Hello, World!');

// Function that uses the lazy constant
function getGreeting()
{
    return laze::read('GREETING');
}
```

### 2. Redefine the Constant in PHPUnit's Bootstrap File

Create a `bootstrap.php` file in your tests directory. This file will be loaded before any tests are executed, allowing you to redefine the constant `GREETING` for testing purposes.

```php
use divengine\laze;

require_once __DIR__ . '/../index.php';

// Redefine the constant `GREETING` only in the test context
laze::define('GREETING', fn() => 'Hello, PHPUnit!');
```

### 3. Write Unit Tests to Verify Behavior

In your tests/LazeTest.php, write tests to ensure that the constant behaves as expected both in normal and test contexts.

```php
use PHPUnit\Framework\TestCase;

class LazeTest extends TestCase
{
    public function testGreeting()
    {
        // Verify that the function getGreeting returns the redefined constant
        $this->assertEquals('Hello, PHPUnit!', getGreeting());
    }

    public function testOriginalGreeting()
    {
        // Check behavior without the PHPUnit bootstrap context
        // Run this without the PHPUnit bootstrap to see the difference
        $this->assertEquals('Hello, World!', getGreeting());
    }
}

```

### 4. Configure PHPUnit to Use the Bootstrap File

Ensure that your phpunit.xml is configured to include the bootstrap file:

```xml
<phpunit bootstrap="tests/bootstrap.php">
    <testsuites>
        <testsuite name="Laze Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### 5. Run the Tests

You can run the tests using the following command:

```bash
phpunit
```

### Expected Outcome

- In the test environment, `getGreeting()` will return `Hello, PHPUnit!`, as the constant `GREETING` has been redefined.
- In a normal (non-test) environment, `getGreeting()` will return `Hello, World!`, using the original definition.

This approach allows you to test your application with different constant values without affecting the production code, providing a powerful way to manage test scenarios with `laze`.

## License

This project is licensed under the GNU GENERAL PUBLIC LICENSE. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue.

## About

`laze` is developed and maintained by [Divengine Software Solutions](https://divengine.com). If you find this library useful, please consider starring the repository and sharing it with others.
