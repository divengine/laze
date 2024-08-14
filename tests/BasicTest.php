<?php

namespace divengine\tests;

use PHPUnit\Framework\TestCase;
use divengine\laze;
use stdClass;
use Closure;

class BasicTest extends TestCase
{
	public function testScalarConstant(): void
	{
		laze::define('FOO', fn() => 42);

		$this->assertTrue(laze::defined('FOO'));
		$this->assertFalse(laze::evaluated('FOO'));

		$foo = laze::read('FOO');

		$this->assertSame(42, $foo);
		$this->assertTrue(laze::evaluated('FOO'));
	}

	public function testObjectInstance(): void
	{
		$object = new stdClass();
		$object->name = 'Laze Test';

		laze::define('BAR', fn() => $object);

		$this->assertTrue(laze::defined('BAR'));
		$this->assertFalse(laze::evaluated('BAR'));

		$bar = laze::read('BAR');

		$this->assertInstanceOf(stdClass::class, $bar);
		$this->assertSame('Laze Test', $bar->name);
		$this->assertTrue(laze::evaluated('BAR'));
	}

	public function testClosureReturningClosure(): void
	{
		laze::define('BAZ', function () {
			return function () {
				return "Nested Closure";
			};
		});

		$this->assertTrue(laze::defined('BAZ'));
		$this->assertFalse(laze::evaluated('BAZ'));

		$baz = laze::read('BAZ');
		$this->assertInstanceOf(Closure::class, $baz);

		$result = $baz();
		$this->assertSame("Nested Closure", $result);
		$this->assertTrue(laze::evaluated('BAZ'));
	}

	public function testConstraintEnforcement(): void
	{
		laze::constraint(
			name: 'QUX must be an integer',
			checker: function ($key, $value) {
				if ($key == 'QUX') {
					return is_int($value);
				}
			}
		);

		laze::define('FOO', fn() => 'HELLO');
		laze::define('QUX', fn() => 42);

		$this->assertTrue(laze::defined('QUX'));

		$qux = laze::read('QUX');
		$this->assertEquals(42, $qux);
	}

	public function testConstraintFailure(): void
	{
		laze::constraint(
			'Must be a string',
			fn($key, $value) => is_string($value)
		);

		laze::define('QUUX', fn() => 42);

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Constraint 'Must be a string' failed for lazy constant: QUUX");

		laze::read('QUUX');
	}

	public function testRedefinition(): void
	{
		$originalValue = 42;
		$redefinedValue = 100;
		$ignoredRedefinition = 200;
		$materializedValue = $redefinedValue;

		// Define the value
		laze::define('CORGE', fn() => $originalValue);

		$this->assertTrue(laze::defined('CORGE'));
		$this->assertFalse(laze::evaluated('CORGE'));

		// Redefine the value
		laze::define('CORGE', fn() => $redefinedValue);

		// Materialize the value
		$corge = laze::read('CORGE');
		$this->assertEquals($redefinedValue, $corge);
		$this->assertTrue(laze::evaluated('CORGE'));

		// Now try to redefine it after read
		laze::define('CORGE', fn() => $ignoredRedefinition);
		$corge = laze::read('CORGE');

		$this->assertEquals($materializedValue, $corge); // Should reflect the materialized value
	}

	protected function tearDown(): void
	{
		// Reset the state of laze between tests
		$reflection = new \ReflectionClass(laze::class);

		$storeProperty = $reflection->getProperty('store');
		$storeProperty->setAccessible(true);
		$storeProperty->setValue($reflection, []);

		$constraintsProperty = $reflection->getProperty('constraints');
		$constraintsProperty->setAccessible(true);
		$constraintsProperty->setValue($reflection, []);

		$evaluatedProperty = $reflection->getProperty('evaluated');
		$evaluatedProperty->setAccessible(true);
		$evaluatedProperty->setValue($reflection, []);
	}
}
