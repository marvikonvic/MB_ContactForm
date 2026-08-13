<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Captcha;

use MB\ContactForm\Model\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class Validator
{
    private const GOOGLE_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';
    private const TURNSTILE_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    private Config $config;
    private Curl $curl;
    private RemoteAddress $remoteAddress;
    private Json $serializer;
    private LoggerInterface $logger;

    public function __construct(
        Config $config,
        Curl $curl,
        RemoteAddress $remoteAddress,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->curl = $curl;
        $this->remoteAddress = $remoteAddress;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function validate(array $requestData, int $storeId): bool
    {
        $provider = $this->config->getCaptchaProvider($storeId);
        if ($provider === 'none') {
            return true;
        }

        $tokenKey = $provider === 'google' ? 'g-recaptcha-response' : 'cf-turnstile-response';
        $token = trim((string)($requestData[$tokenKey] ?? ''));
        $secret = $this->config->getCaptchaSecretKey($storeId);
        if ($token === '' || $secret === '') {
            return false;
        }

        $endpoint = $provider === 'google' ? self::GOOGLE_ENDPOINT : self::TURNSTILE_ENDPOINT;
        $payload = ['secret' => $secret, 'response' => $token];
        $remoteIp = $this->remoteAddress->getRemoteAddress();
        if (is_string($remoteIp) && $remoteIp !== '') {
            $payload['remoteip'] = $remoteIp;
        }

        try {
            $this->curl->setTimeout(10);
            $this->curl->post($endpoint, $payload);
            if ($this->curl->getStatus() !== 200) {
                return false;
            }
            $response = $this->serializer->unserialize($this->curl->getBody());
            return is_array($response) && ($response['success'] ?? false) === true;
        } catch (\Throwable $exception) {
            $this->logger->error('MB Contact Form CAPTCHA verification failed.', ['exception' => $exception]);
            return false;
        }
    }
}
