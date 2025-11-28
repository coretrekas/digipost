# Contributing

Thank you for considering contributing to the Digipost PHP SDK! This document provides guidelines and instructions for contributing.

## Code of Conduct

Please be respectful and considerate in all interactions. We welcome contributions from everyone.

## Getting Started

1. Fork the repository
2. Clone your fork:
   ```bash
   git clone https://github.com/your-username/digipost.git
   cd digipost
   ```
3. Install dependencies:
   ```bash
   composer install
   ```

## Development Workflow

### Running Tests

We use [Pest PHP](https://pestphp.com/) for testing:

```bash
./vendor/bin/pest
```

Run a specific test file:

```bash
./vendor/bin/pest tests/Unit/SenderIdTest.php
```

Run tests with coverage:

```bash
./vendor/bin/pest --coverage
```

### Code Style

We use [Laravel Pint](https://laravel.com/docs/pint) for code formatting:

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

### Static Analysis

We use [PHPStan](https://phpstan.org/) at level 9 (strictest):

```bash
./vendor/bin/phpstan analyse
```

### Rector

We use [Rector](https://getrector.com/) for automated code improvements:

```bash
# Preview changes
./vendor/bin/rector --dry-run

# Apply changes
./vendor/bin/rector
```

### Running All Checks

Before submitting a pull request, run all checks:

```bash
./vendor/bin/pint && ./vendor/bin/phpstan analyse && ./vendor/bin/pest
```

## Pull Request Process

1. Create a new branch for your feature or fix:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes and ensure:
   - All tests pass
   - Code style is correct (run Pint)
   - PHPStan shows no errors
   - New features have tests
   - Documentation is updated if needed

3. Commit your changes with a descriptive message:
   ```bash
   git commit -m "Add feature: description of your changes"
   ```

4. Push to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

5. Open a Pull Request against the `main` branch

### Pull Request Guidelines

- Keep PRs focused on a single feature or fix
- Write clear commit messages
- Add tests for new functionality
- Update documentation as needed
- Ensure CI passes before requesting review

## Writing Tests

Tests are located in the `tests/` directory:

- `tests/Unit/` - Unit tests for individual classes
- `tests/Feature/` - Feature/integration tests

Example test:

```php
<?php

declare(strict_types=1);

use Coretrek\Digipost\SenderId;

it('creates a sender id from integer', function () {
    $senderId = SenderId::of(123456);

    expect($senderId->value)->toBe(123456);
    expect((string) $senderId)->toBe('123456');
});

it('throws exception for invalid sender id', function () {
    SenderId::of(-1);
})->throws(InvalidArgumentException::class);
```

## Adding New Features

When adding new features:

1. **Representations** - Add data classes in `src/Representations/`
2. **API Methods** - Add API methods in the appropriate `src/Api/` class
3. **Client Methods** - Expose through `DigipostClient` if needed
4. **Tests** - Add comprehensive tests
5. **Examples** - Consider adding an example in `examples/`
6. **Documentation** - Update README if needed

## Reporting Issues

When reporting issues, please include:

- PHP version
- Package version
- Steps to reproduce
- Expected behavior
- Actual behavior
- Error messages (if any)

## Security Vulnerabilities

If you discover a security vulnerability, please email security@coretrek.no instead of using the issue tracker.

## Questions

If you have questions, feel free to open an issue with the "question" label.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

