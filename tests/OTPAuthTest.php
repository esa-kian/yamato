<?php

use PHPUnit\Framework\TestCase;
use Esakian\Yamato\OTPAuth;
use Esakian\Yamato\RedisRateLimiter;

class OTPAuthTest extends TestCase
{
    private $rateLimiterMock;
    private $otpAuth;

    protected function setUp(): void
    {
        $this->rateLimiterMock = $this->createMock(RedisRateLimiter::class);
        $this->otpAuth = new OTPAuth($this->rateLimiterMock, 300); // 5-minute OTP expiration
    }

    public function testGenerateOTP(): void
    {
        $this->rateLimiterMock->expects($this->once())
            ->method('canGenerateOTP')
            ->with('test_user')
            ->willReturn(true);

        $this->rateLimiterMock->expects($this->once())
            ->method('storeOTP')
            ->with('test_user', $this->isType('integer'), 300);

        $otp = $this->otpAuth->generateOTP('test_user');

        $this->assertIsInt($otp);
        $this->assertGreaterThanOrEqual(100000, $otp);
        $this->assertLessThanOrEqual(999999, $otp);
    }

    public function testGenerateOTPThrowsExceptionWhenRateLimited(): void
    {
        $this->rateLimiterMock->expects($this->once())
            ->method('canGenerateOTP')
            ->with('test_user')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Rate limit exceeded. Try again later.');

        $this->otpAuth->generateOTP('test_user');
    }

    public function testValidateOTP(): void
    {
        $this->assertFalse($this->otpAuth->validateOTP('test_user', 654321));
    }
}
