# FAQ

## What is a lazy immutable value?

It is a value defined as a closure that is evaluated only on first read. The
result is cached and cannot be replaced afterward.

## What happens if I read an undefined key?

`laze::read()` throws an exception if the key was never defined. Use
`laze::defined()` before reading if the key is optional.

## Can I redefine a key?

Yes, as long as it has not been evaluated. Once you call `read`, the value is
materialized and further `define` calls are ignored.

## Can values be objects or closures?

Yes. A closure can return any value, including objects or other closures.
See `tests/BasicTest.php` for patterns.

## How do constraints work?

Constraints are global checks that run when a value is evaluated. If a
constraint returns `false`, `read` throws an exception. Always return `true`
for keys you are not validating.

## How do I check if a key was evaluated?

Use `laze::evaluated($key)`. This also throws if the key is undefined.

## How do I reset state during tests?

Because Laze uses static storage, reset the internal arrays via reflection or
run tests with `--process-isolation`.
