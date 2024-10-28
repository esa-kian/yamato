<?php

namespace Esakian\Yamato;

use Predis\Client;

class RedisRateLimiter
{
    private $redis;
    private $rateLimit;
    private $rateLimitPeriod;

    public function __construct(Client $redis, int $rateLimit = 5, int $rateLimitPeriod = 60)
    {
        $this->redis = $redis;
        $this->rateLimit = $rateLimit;
        $this->rateLimitPeriod = $rateLimitPeriod;
    }

    public function canGenerateOTP(string $identifier): bool
    {
        $key = "otp_rate_limit:{$identifier}";
        $attempts = $this->redis->incr($key);

        if ($attempts > 1) {
            $this->redis->expire($key, $this->rateLimitPeriod);
        }

        return $attempts <= $this->rateLimit;
    }

    public function storeOTP(string $identifier, int $otp, int $expiration): void
    {
        $key = "otp:{$identifier}";
        $this->redis->setex($key, $expiration, $otp);
    }

    public function getOTP(string $identifier): ?int
    {
        $key = "otp:{$identifier}";
        return (int) $this->redis->get($key) ?: null;
    }
}
