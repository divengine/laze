# Div PHP Laze

**laze** is a PHP library for defining lazy constants. Values are set as closures and only materialize upon first access, ensuring efficient and controlled initialization. Once a value is evaluated, it becomes immutable and cannot be redefined as a value, though it can be redefined as a closure until it's accessed.

**Laze** might be an English word that suggests relaxation or laziness, but in this context, it’s actually an acronym derived from **lazy Evaluation**. This refers to a programming technique where the evaluation of an expression is delayed until its value is needed. With `laze` once the value is evaluated, it **becomes an immutable constant**. In other words, a value that, although evaluated with delay, cannot be modified after its initial evaluation. Thus, `laze` encapsulates the concept of deferred evaluation that results in definitive constancy, combining flexibility and robustness into one concept.

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

`laze` can be particularly useful in testing environments where you need to redefine constants differently from their production values. This example demonstrates how to define a lazy constant in a standard PHP file and then override it during unit tests.

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

## Best Practices for Using Laze

### 1. Naming Conventions

When defining and reading constants with `laze`, it's recommended to define the name of each Lazy constant in a separate constant. This practice helps avoid hardcoding strings throughout your code and makes your constants easier to manage and update. For example:

```php
// Define the constant name separately:
define('C_MAX_USERS', 'MAX_USERS');

// Use it with Laze:
Laze::define(C_MAX_USERS, function() {
    return getMaxUsersFromConfig();
});

You can also use a more descriptive name:
```

```php
// Define with a more descriptive name:
define('C_MAX_USERS', 'global.constants.max_users');

// Use it with Laze:
Laze::define(C_MAX_USERS, function() {
    return getMaxUsersFromConfig();
});
```

By using separate constants for Lazy constant names, you enhance code clarity, maintainability, and reduce the risk of errors.

### 2. Handling Non-Existent Constants

If you attempt to read a constant with Laze that has not been defined, an exception will be thrown, ensuring strict constant management. This behavior is intentional to prevent undefined or incorrectly defined constants from causing errors in your application. Here’s how it works:

```php

try {
    $maxUsers = Laze::read(C_MAX_USERS);
} catch (\Exception $e) {
    // Handle the undefined constant scenario
    echo $e->getMessage();
}
```

In this example, if `C_MAX_USERS` has not been defined using `laze::define`, an exception will be raised with a message like **"Undefined lazy constant: MAX_USERS"**. Always ensure that constants are properly defined before attempting to read them to avoid such exceptions.

## Documentation

For more detailed documentation and advanced topics, please refer to our official website at [divengine.org](https://divengine.org). There, you'll find comprehensive guides, examples, and further resources to help you make the most out of `laze`.

## License

This project is licensed under the GNU GENERAL PUBLIC LICENSE. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue.

## About

`laze` is developed and maintained by [Divengine Software Solutions](https://divengine.com). If you find this library useful, please consider starring the repository and sharing it with others.
