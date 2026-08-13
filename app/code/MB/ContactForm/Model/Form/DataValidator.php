<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Form;

use MB\ContactForm\Model\Config;
use Magento\Framework\Exception\LocalizedException;

class DataValidator
{
    private const MAX_MESSAGE_LENGTH = 5000;
    private const MAX_SHORT_LENGTH = 255;
    private const EMAIL_PATTERN = '/\A[A-Za-z0-9_+\-]+(?:\.[A-Za-z0-9_+\-]+)*'
        . '@[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?'
        . '(?:\.[A-Za-z0-9](?:[A-Za-z0-9\-]{0,61}[A-Za-z0-9])?)+\z/';

    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function validate(array $data, int $storeId): array
    {
        $clean = [
            'message' => $this->normalizeText((string)($data['message'] ?? '')),
            'firstname' => $this->normalizeText((string)($data['firstname'] ?? '')),
            'lastname' => $this->normalizeText((string)($data['lastname'] ?? '')),
            'email' => $this->normalizeText((string)($data['email'] ?? '')),
            'company' => $this->normalizeText((string)($data['company'] ?? '')),
            'telephone' => $this->normalizeText((string)($data['telephone'] ?? '')),
            'custom' => [],
        ];

        $this->assertRequired($clean['message'], $this->config->getLabel('message', $storeId));
        $this->assertRequired($clean['firstname'], $this->config->getLabel('firstname', $storeId));
        $this->assertRequired($clean['lastname'], $this->config->getLabel('lastname', $storeId));
        $this->assertRequired($clean['email'], $this->config->getLabel('email', $storeId));

        $this->assertLength($clean['message'], self::MAX_MESSAGE_LENGTH, $this->config->getLabel('message', $storeId));
        $this->assertLength($clean['firstname'], 100, $this->config->getLabel('firstname', $storeId));
        $this->assertLength($clean['lastname'], 100, $this->config->getLabel('lastname', $storeId));
        $this->assertLength($clean['email'], 254, $this->config->getLabel('email', $storeId));
        $this->assertLength($clean['company'], 150, $this->config->getLabel('company', $storeId));
        $this->assertLength($clean['telephone'], 30, $this->config->getLabel('telephone', $storeId));

        $this->assertPattern($clean['message'], 'safe_text', $this->config->getLabel('message', $storeId));
        $this->assertPattern($clean['firstname'], 'letters', $this->config->getLabel('firstname', $storeId));
        $this->assertPattern($clean['lastname'], 'letters', $this->config->getLabel('lastname', $storeId));
        $this->assertPattern($clean['email'], 'email', $this->config->getLabel('email', $storeId));
        $this->assertPattern($clean['company'], 'alphanumeric', $this->config->getLabel('company', $storeId));
        $this->assertPattern($clean['telephone'], 'numbers', $this->config->getLabel('telephone', $storeId));

        $postedCustom = isset($data['custom']) && is_array($data['custom']) ? $data['custom'] : [];
        foreach ($this->config->getCustomFields($storeId) as $definition) {
            $value = $this->normalizeText((string)($postedCustom[$definition['code']] ?? ''));
            if ($definition['required']) {
                $this->assertRequired($value, $definition['label']);
            }
            $this->assertLength(
                $value,
                $definition['type'] === 'textarea' ? 1000 : self::MAX_SHORT_LENGTH,
                $definition['label']
            );
            if ($definition['type'] === 'select' && $value !== '' && !in_array($value, $definition['options'], true)) {
                throw new LocalizedException(__('The value selected for "%1" is not valid.', $definition['label']));
            }
            $this->assertPattern($value, $definition['validation'], $definition['label']);
            $clean['custom'][] = [
                'code' => $definition['code'],
                'label' => $definition['label'],
                'value' => $value,
            ];
        }

        return $clean;
    }

    private function assertRequired(string $value, string $label): void
    {
        if ($value === '') {
            throw new LocalizedException(__('Field "%1" is required.', $label));
        }
    }

    private function assertLength(string $value, int $maxLength, string $label): void
    {
        if (mb_strlen($value) > $maxLength) {
            throw new LocalizedException(__('Field "%1" is too long. Maximum length is %2 characters.', $label, $maxLength));
        }
    }

    private function assertPattern(string $value, string $validation, string $label): void
    {
        if ($value === '' || $validation === 'none') {
            return;
        }

        $isValid = match ($validation) {
            'letters' => (bool)preg_match(
                '/\A(?=.*[\p{Latin}\p{Cyrillic}])[\p{Latin}\p{Cyrillic}\x{0300}-\x{036F}\p{Zs}]+\z/u',
                $value
            ),
            'alphanumeric' => (bool)preg_match(
                '/\A(?=.*[\p{Latin}\p{Cyrillic}0-9])[\p{Latin}\p{Cyrillic}\x{0300}-\x{036F}0-9\p{Zs}]+\z/u',
                $value
            ),
            'numbers' => (bool)preg_match('/\A[0-9]+\z/', $value),
            'email' => (bool)preg_match(self::EMAIL_PATTERN, $value)
                && filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'safe_text' => (bool)preg_match(
                '/\A[\p{Latin}\p{Cyrillic}\x{0300}-\x{036F}0-9\p{P}\p{Zs}\r\n\t]+\z/u',
                $value
            ),
            default => true,
        };

        if (!$isValid) {
            throw new LocalizedException(__('Field "%1" contains characters that are not allowed.', $label));
        }
    }

    private function normalizeText(string $value): string
    {
        $value = trim($value);
        if (!class_exists(\Normalizer::class)) {
            return $value;
        }

        $normalized = \Normalizer::normalize($value, \Normalizer::FORM_C);
        return is_string($normalized) ? $normalized : $value;
    }
}
