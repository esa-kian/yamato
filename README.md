# Yamato

**Yamato** is a PHP package for generating and validating One-Time Passwords (OTPs) with Redis rate limiting. It’s ideal for adding secure, time-limited authentication flows to your PHP application.

## Features

- Generates and validates OTPs.
- Enforces rate limiting using Redis to prevent OTP abuse.
- Configurable OTP expiration time and rate limits.

## Installation

Install Yamato via Composer:

```bash
composer require esakian/yamato
```

Make sure to install Predis or any other Redis client compatible with your PHP setup, as it’s required for rate limiting.

## Usage

1. **Setup**
Initialize a Redis client and pass it to the package:
```php
require 'vendor/autoload.php';

use Predis\Client;
use YourVendor\OTPAuth\OTPAuth;
use YourVendor\OTPAuth\RedisRateLimiter;

// Initialize Redis client
$redis = new Client();

// Configure Redis rate limiter (5 attempts per minute)
$rateLimiter = new RedisRateLimiter($redis, 5, 60);

// Configure OTP authentication with a 5-minute OTP expiration
$otpAuth = new OTPAuth($rateLimiter, 300);
```

2. **Generating an OTP**
To generate an OTP for a given identifier (e.g., an email or user ID):

```php
try {
    $identifier = 'user@example.com';
    $otp = $otpAuth->generateOTP($identifier);
    echo "Your OTP is: {$otp}\n";
} catch (Exception $e) {
    echo $e->getMessage(); // Handle rate limit exceptions
}
```

3. **Validating an OTP**
To validate an OTP provided by the user:

```php
$isValid = $otpAuth->validateOTP('user@example.com', $otp);
if ($isValid) {
    echo "OTP is valid.\n";
} else {
    echo "OTP is invalid or expired.\n";
}
```

## Configuration
You can customize the OTP expiration time and rate limit as shown below:

```php
// RedisRateLimiter configuration
$rateLimit = 5; // maximum 5 attempts
$rateLimitPeriod = 60; // within 60 seconds
$rateLimiter = new RedisRateLimiter($redis, $rateLimit, $rateLimitPeriod);

// OTPAuth configuration
$otpExpiration = 300; // OTP expires in 5 minutes
$otpAuth = new OTPAuth($rateLimiter, $otpExpiration);
```

## Unit Tests
To run the unit tests:

1. Install development dependencies:

```bash
composer install --dev
```
2. Run PHPUnit:

```bash
vendor/bin/phpunit
```

## Requirements
- PHP >= 7.4
- Redis server
- Predis (or equivalent Redis client)

## License
This package is open-source software licensed under the MIT license.

