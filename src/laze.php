<?php

namespace divengine;

use Closure;

/**
 * [[]] Div PHP Laze
 *
 * A PHP library for defining lazy immutable values. Values are set as closures 
 * and only materialize upon first access, ensuring efficient and controlled
 * initialization.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program as the file LICENSE.txt; if not, please see
 * https://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package divengine/laze
 * @author  Rafa Rodriguez @rafageist [https://rafageist.com]
 *
 * @link    https://divengine.org
 * @link    https://github.com/divengine/div
 */

class laze
{
    /**
     * Version of the library.
     * @var string
     */
    private static string $__version = '1.1.1'; 
	
    /**
     * Store for lazy immutable values.
     * @var array<string, mixed>
     */
    private static array $store = [];

    /**
     * Store for constraints.
     * @var array<int, array{0: string, 1: callable}>
     */
    private static array $constraints = [];

    /**
     * Map of evaluated lazy immutable values.
     * @var array<bool>
     */
    private static array $evaluated = [];

    /**
     * Get the version of the library.
     * 
     * @return mixed
     */
    public static function getVersion()
    {
        return self::$__version;
    }

    /**
     * Check if a lazy immutable value is defined.
     * 
     * @param string $key
     * @return bool
     */
    public static function defined(string $key): bool
    {
        return isset(self::$store[$key]);
    }

    /**
     * Check if a lazy immutable value has been evaluated.
     * 
     * @param string $key
     * @return bool
     */
    public static function evaluated(string $key): bool
    {
        self::defined($key) or throw new \Exception("Undefined lazy immutable value: $key");

        return self::$evaluated[$key];
    }

    /**
     * Define a constraint for a lazy immutable value.
     * 
     * @param string $name
     * @param callable $checker
     * @return void
     */
    public static function constraint($name, callable $checker): void
    {
        self::$constraints[] = [$name, $checker];
    }

    /**
     * Define a lazy immutable value as a closure.
     * 
     * @param string $key
     * @param callable $value
     * @return void
     */
    public static function define(string $key, callable $value): void
    {
        if (!self::defined($key) || !self::evaluated($key)) {
            self::$store[$key] = $value;
            self::$evaluated[$key] = false;
        }
    }

    /**
     * Read the value of a lazy immutable value, evaluating the closure if needed.
     * 
     * @param string $key
     * @return mixed
     */
    public static function read(string $key): mixed
    {
        if (!self::evaluated($key) && is_callable(self::$store[$key])) {
			$value = self::$store[$key]();

            foreach (self::$constraints as $constraint) {
                $pass = $constraint[1]($key, $value);
                if (!$pass) {
                    throw new \Exception("Constraint '{$constraint[0]}' failed for lazy immutable value: $key");
                }
            }

            self::$store[$key] = $value;
            self::$evaluated[$key] = true;
        }

        return self::$store[$key];
    }
}
