<?php

namespace Esakian\Yamato;

use Esakian\Yamato\RedisRateLimiter;

class OTPAuth
{
    private $redisLimiter;
    private $otpExpiration;

    public function __construct(RedisRateLimiter $redisLimiter, int $otpExpiration = 300)
    {
        $this->redisLimiter = $redisLimiter;
        $this->otpExpiration = $otpExpiration;
    }

    public function generateOTP(string $identifier): int
    {
        if (!$this->redisLimiter->canGenerateOTP($identifier)) {
            throw new \Exception('Rate limit exceeded. Try again later.');
        }

        $otp = random_int(100000, 999999);
        $this->redisLimiter->storeOTP($identifier, $otp, $this->otpExpiration);

        return $otp;
    }

    public function validateOTP(string $identifier, int $otp): bool
    {
        return $this->redisLimiter->getOTP($identifier) === $otp;
    }
}
