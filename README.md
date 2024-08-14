# Div PHP Laze

**laze** is a PHP library designed for defining _lazy evaluation_. Values are set as closures and only materialize upon first access, ensuring efficient and controlled initialization. Once a closure function is evaluated, it becomes immutable and cannot be redefined as a different value. However, it can be redefined as a `closure` until it’s accessed, at which point it transforms into a non-closure value.

**laze** might be an English word that suggests relaxation or laziness, but in this context, it’s actually an acronym derived from **Lazy Evaluation**. This refers to a programming technique where the evaluation of an expression is delayed until its value is needed. With **laze**, once the value is evaluated, it **becomes an immutable value**. In other words, a value that, although evaluated with delay, cannot be modified after its initial evaluation. Thus, **laze** encapsulates the concept of deferred evaluation that results in a definitive value, combining flexibility and robustness into one concept.

## Installation

You can install **laze** using Composer:

```bash
composer require divengine/laze
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

## Comprehensive example

This example covers the full range of Laze's capabilities in a concise manner suitable for a README, showing how it can be applied in real-world scenarios.

- Constraints: Ensures that APP_CONFIG implements the Configurable interface.
- Closure Returning Closure: MY_FUNCTION key holds a closure that returns another closure
- Lazy Value with Object Instance: APP_CONFIG stores an instance of AppConfig, which is validated by the constraint.
- Reusing define and read: FINAL_MESSAGE reads from both APP_CONFIG and MY_FUNCTION, combining their values.
- PHPUnit Test: Demonstrates how APP_CONFIG can be redefined for testing, using a mock object.

```php

interface Configurable {
    public function configure(array $settings): void;
}

class AppConfig implements Configurable {
    private array $settings;

    public function configure(array $settings): void {
        $this->settings = $settings;
    }

    public function getSetting(string $key) {
        return $this->settings[$key] ?? null;
    }
}

// 1. Add a constraint to ensure a value implements the Configurable interface
laze::constraint(
    name: 'Must implement Configurable interface',
    fn($key, $value) => $key == 'APP_CONFIG' ? $value instanceof Configurable : true
);

// 2. Define a lazy value that returns a closure
laze::define('MY_FUNCTION', function() {
    return function() {
        return "Function Result";
    };
});

// 3. Define a lazy value with an object instance
laze::define('APP_CONFIG', function() {
    $config = new AppConfig();
    $config->configure([
        'timezone' => 'UTC',
        'locale' => 'en_US'
    ]);
    return $config;
});

// 4. Reuse define and read within each other
laze::define('FINAL_MESSAGE', function() {
    $config = laze::read('APP_CONFIG');
    $timezone = $config->getSetting('timezone');
    return laze::read('MY_FUNCTION')() . " in timezone $timezone";
});

$finalMessage = laze::read('FINAL_MESSAGE');
echo $finalMessage; // Outputs: "Function Result in timezone UTC"

// 5. PHPUnit Test - Redefining a value
class LazeTest extends \PHPUnit\Framework\TestCase {
    public function testAppConfigCanBeMocked() {

        // mock function
        laze::define('MY_FUNCTION', function() {
            return function() {
                return "Mocked Result";
            };
        });

        // mock object
        laze::define('APP_CONFIG', function() {
            $mockConfig = $this->createMock(Configurable::class);
            $mockConfig->method('getSetting')->willReturn('mocked_timezone');
            return $mockConfig;
        });

        $message = laze::read('FINAL_MESSAGE');
        $this->assertEquals("Mocked Result in timezone mocked_timezone", $message);
    }
}

```

## Utility of this library

- **Lazy Evaluation**: Optimizes resource usage by deferring value evaluation until needed, improving performance and load times.

- **Immutability**: Ensures values remain unchanged once evaluated, useful in concurrent environments and functional programming.

- **Dependency Injection**: Supports lazy initialization of dependencies, improving modularity and testing (e.g., mocking services).

- **Configuration Management**: Manages environment-specific or conditional configurations, evaluated only when required.

- **Caching**: Implements deferred caching, storing results only when needed, and supports multi-level caching with constraints.

- **Event-Driven Programming**: Facilitates deferred event handling, triggering actions only under certain conditions or upon request.

- **Testing**: Validates values with constraints in unit tests, and simulates complex environments with lazy-loaded dependencies.

- **Security**: Enforces data validation and security constraints before values are used, reducing risks.

- **Declarative Programming**: Supports declarative configurations that are evaluated on demand.

- **Domain-Specific Languages (DSLs)**: Builds DSLs with declarative value definitions that execute in specific contexts.

- **CI/CD**: Defines dynamic configurations or scripts for CI/CD pipelines, evaluated conditionally based on environment state.

## License

This project is licensed under the GNU GENERAL PUBLIC LICENSE. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or open an Issue.

## About

`laze` is developed and maintained by [Divengine Software Solutions](https://divengine.com). If you find this library useful, please consider starring the repository and sharing it with others.
