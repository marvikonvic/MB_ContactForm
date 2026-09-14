<?php
declare(strict_types=1);

namespace MB\ContactForm\Test\Unit\Model\Captcha;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\Serializer\Json;
use MB\ContactForm\Model\Captcha\Validator;
use MB\ContactForm\Model\Config;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ValidatorTest extends TestCase
{
    private $config;
    private $curl;
    private $remote;
    private $logger;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->curl = $this->createMock(Curl::class);
        $this->remote = $this->createMock(RemoteAddress::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function subject(string $provider = 'google', string $secret = 'test-secret'): Validator
    {
        $this->config->method('getCaptchaProvider')->with(2)->willReturn($provider);
        $this->config->method('getCaptchaSecretKey')->with(2)->willReturn($secret);
        return new Validator($this->config, $this->curl, $this->remote, new Json(), $this->logger);
    }

    public function testDisabledCaptchaDoesNotCallProvider(): void
    {
        $this->curl->expects(self::never())->method('post');
        self::assertTrue($this->subject('none')->validate([], 2));
    }

    /** @dataProvider missingCredentials */
    public function testMissingCredentialsFailWithoutHttpRequest(array $data, string $secret): void
    {
        $this->curl->expects(self::never())->method('post');
        self::assertFalse($this->subject('google', $secret)->validate($data, 2));
    }

    public static function missingCredentials(): array
    {
        return [[[], 'secret'], [['g-recaptcha-response' => '  '], 'secret'],
            [['g-recaptcha-response' => 'token'], '']];
    }

    /** @dataProvider providers */
    public function testSendsTokenToCorrectProvider(string $provider, string $key, string $url, $ip): void
    {
        $payload = ['secret' => 'test-secret', 'response' => 'token'];
        if (is_string($ip) && $ip !== '') {
            $payload['remoteip'] = $ip;
        }
        $this->remote->method('getRemoteAddress')->willReturn($ip);
        $this->curl->expects(self::once())->method('post')->with($url, $payload);
        $this->curl->method('getStatus')->willReturn(200);
        $this->curl->method('getBody')->willReturn('{"success":true}');
        self::assertTrue($this->subject($provider)->validate([$key => ' token '], 2));
    }

    public static function providers(): array
    {
        return [
            ['google', 'g-recaptcha-response', 'https://www.google.com/recaptcha/api/siteverify', '192.0.2.1'],
            ['turnstile', 'cf-turnstile-response', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', false],
        ];
    }

    /** @dataProvider rejectedResponses */
    public function testFailsClosedOnRejectedProviderResponse(int $status, string $body): void
    {
        $this->curl->method('getStatus')->willReturn($status);
        $this->curl->method('getBody')->willReturn($body);
        self::assertFalse($this->subject()->validate(['g-recaptcha-response' => 'token'], 2));
    }

    public static function rejectedResponses(): array
    {
        return [
            [500, '{"success":true}'], [403, '{"success":true}'],
            [200, '{"success":false,"error-codes":["timeout-or-duplicate"]}'],
            [200, '{}'], [200, '{"success":"true"}'], [200, '{"success":1}'],
            [200, 'null'], [200, 'true'],
        ];
    }

    public function testInvalidJsonIsLoggedAndRejected(): void
    {
        $this->curl->method('getStatus')->willReturn(200);
        $this->curl->method('getBody')->willReturn('invalid json');
        $this->logger->expects(self::once())->method('error')->with(
            'MB Contact Form CAPTCHA verification failed.',
            self::callback(static fn(array $context): bool => $context['exception'] instanceof \InvalidArgumentException)
        );
        self::assertFalse($this->subject()->validate(['g-recaptcha-response' => 'token'], 2));
    }

    public function testNetworkExceptionIsLoggedAndRejected(): void
    {
        $exception = new \RuntimeException('Simulated timeout');
        $this->curl->method('post')->willThrowException($exception);
        $this->logger->expects(self::once())->method('error')->with(
            'MB Contact Form CAPTCHA verification failed.', ['exception' => $exception]
        );
        self::assertFalse($this->subject()->validate(['g-recaptcha-response' => 'token'], 2));
    }
}
