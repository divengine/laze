<?php

namespace divengine;

use Closure;

/**
 * [[]] Div PHP Laze
 *
 * A PHP library for defining lazy constants. Values are set as closures 
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
 * @version 1.0.0
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
    private static string $__version = '1.0.0'; 
	
    /**
     * Store for lazy constants.
     * @var array<string, mixed>
     */
    private static array $store = [];

    /**
     * Store for constraints.
     * @var array<string, array<string, callable>>
     */
    private static array $constraints = [];

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
     * Define a lazy constant as a closure.
     * 
     * @param string $key
     * @param callable $value
     * @return void
     */
    public static function define(string $key, callable $value): void
    {
        if (!self::defined($key) || is_callable(self::$store[$key])) {
            self::$store[$key] = $value;
        }
    }

    /**
     * Check if a lazy constant is defined.
     * 
     * @param string $key
     * @return bool
     */
    public static function defined(string $key): bool
    {
        return isset(self::$store[$key]);
    }

    /**
     * Read the value of a lazy constant, evaluating the closure if needed.
     * 
     * @param string $key
     * @return mixed
     */
    public static function read(string $key): mixed
    {
        if (!self::defined($key)) {
            throw new \Exception("Undefined lazy constant: $key");
        }

		$value = self::$store[$key];

        if (is_callable($value)) {
			$value = $value();

            foreach (self::$constraints as $constraint) {
                $pass = $constraint[1]($key, $value);
                if (!$pass) {
                    throw new \Exception("Constraint '{$constraint[0]}' failed for lazy constant: $key");
                }
            }

            self::$store[$key] = $value;
        }

        return $value;
    }

    /**
     * Define a constraint for a lazy constant.
     * 
     * @param string $name
     * @param callable $checker
     * @return void
     */
    public static function constraint($name, callable $checker): void
    {
        self::$constraints[] = [$name, $checker];
    }
}
