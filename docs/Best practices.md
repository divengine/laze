# Best practices

## 1) Keep key names centralized

Avoid hard-coded strings by defining key names as constants.

```php
<?php

define('C_MAX_USERS', 'app.max_users');

laze::define(C_MAX_USERS, fn () => 100);
```

## 2) Treat closures as pure computations

Closures run once and become the stored value. Keep them deterministic and
avoid side effects when possible.

## 3) Use constraints carefully

Constraints run on every evaluation. Return `true` for keys you are not
validating so unrelated reads do not fail.

```php
<?php

laze::constraint(
    'Max users must be an int',
    fn ($key, $value) => $key === C_MAX_USERS ? is_int($value) : true
);
```

## 4) Redefine only before evaluation

You can redefine a key while it is still unevaluated. After the first `read`,
the value becomes immutable.

## 5) Guard reads

`laze::read()` throws if the key is not defined. Use `laze::defined()` or
wrap reads in a `try/catch` when the key might be missing.

## 6) Reset state in tests

Laze uses static storage. If you need isolation, reset the internal arrays
via reflection or run tests with process isolation.
