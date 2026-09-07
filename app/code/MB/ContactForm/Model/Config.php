<?php
declare(strict_types=1);

namespace MB\ContactForm\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

class Config
{
    public const XML_PREFIX = 'mb_contactform/';

    private ScopeConfigInterface $scopeConfig;
    private EncryptorInterface $encryptor;
    private Json $serializer;
    private LoggerInterface $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PREFIX . 'general/enabled', ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isAllowedForGroup(int $groupId, ?int $storeId = null): bool
    {
        $raw = trim($this->getValue('general/allowed_customer_groups', $storeId));
        if ($raw === '') {
            return true;
        }

        $groups = array_filter(array_map('trim', explode(',', $raw)), 'strlen');
        return in_array((string)$groupId, $groups, true);
    }

    public function getFormTitle(?int $storeId = null): string
    {
        return $this->getValue('general/form_title', $storeId);
    }

    public function getRecipientEmail(?int $storeId = null): string
    {
        return $this->getValue('general/recipient_email', $storeId);
    }

    public function getSenderEmail(?int $storeId = null): string
    {
        return $this->getValue('general/sender_email', $storeId);
    }

    public function getSenderName(?int $storeId = null): string
    {
        return $this->getValue('general/sender_name', $storeId);
    }

    public function getEmailTemplate(?int $storeId = null): string
    {
        return $this->getValue('general/email_template', $storeId)
            ?: 'mb_contactform_general_email_template';
    }

    public function getSuccessUrl(?int $storeId = null): string
    {
        return $this->getValue('general/success_url', $storeId);
    }

    public function getSuccessMessage(?int $storeId = null): string
    {
        return $this->getValue('general/success_message', $storeId);
    }

    public function getSuccessMetaTitle(?int $storeId = null): string
    {
        return $this->getValue('general/success_meta_title', $storeId);
    }

    public function getSuccessMetaDescription(?int $storeId = null): string
    {
        return $this->getValue('general/success_meta_description', $storeId);
    }

    public function getNewsletterUrl(?int $storeId = null): string
    {
        return $this->getValue('general/newsletter_url', $storeId);
    }

    public function getNewsletterMessage(?int $storeId = null): string
    {
        return $this->getValue('general/newsletter_message', $storeId);
    }

    public function getLabel(string $field, ?int $storeId = null): string
    {
        return $this->getValue('labels/' . $field, $storeId);
    }

    public function getStandardFieldRows(?int $storeId = null): array
    {
        $schema = new StandardFields();
        $labels = [];
        foreach (StandardFields::CODES as $code) {
            $labels[$code] = $this->getLabel($code, $storeId);
        }
        $defaults = $schema->defaults(array_filter($labels, 'strlen'));
        $raw = $this->scopeConfig->getValue(self::XML_PREFIX . 'labels/rows', ScopeInterface::SCOPE_STORE, $storeId);
        if ($raw === null || $raw === '') {
            return $defaults;
        }
        try {
            $rows = is_array($raw) ? $raw : $this->serializer->unserialize($raw);
            return $schema->normalize(is_array($rows) ? $rows : []);
        } catch (\Throwable $exception) {
            $this->logger->error('Invalid MB Contact Form standard fields configuration.', ['exception' => $exception]);
            return $defaults;
        }
    }

    public function getStandardFields(?int $storeId = null): array
    {
        $fields = [];
        foreach ($this->getStandardFieldRows($storeId) as $row) {
            if ($row['disabled'] === '1') {
                continue;
            }
            $row['required'] = $row['required'] === '1';
            $row['options'] = array_values(array_filter(array_map('trim', explode(',', $row['options'])), 'strlen'));
            $row['maxlength'] = StandardFields::LIMITS[$row['code']];
            $row['name'] = $row['code'];
            $fields[] = $row;
        }
        usort($fields, static fn(array $a, array $b): int => $a['sort_order'] <=> $b['sort_order']);
        return $fields;
    }

    public function getCaptchaProvider(?int $storeId = null): string
    {
        $provider = $this->getValue('captcha/provider', $storeId);
        return in_array($provider, ['google', 'turnstile'], true) ? $provider : 'none';
    }

    public function getCaptchaSiteKey(?int $storeId = null): string
    {
        $provider = $this->getCaptchaProvider($storeId);
        return $provider === 'google'
            ? $this->getValue('captcha/google_site_key', $storeId)
            : ($provider === 'turnstile' ? $this->getValue('captcha/turnstile_site_key', $storeId) : '');
    }

    public function getCaptchaSecretKey(?int $storeId = null): string
    {
        $provider = $this->getCaptchaProvider($storeId);
        $path = $provider === 'google' ? 'captcha/google_secret_key' : 'captcha/turnstile_secret_key';
        if ($provider === 'none') {
            return '';
        }

        $encrypted = $this->getValue($path, $storeId);
        if ($encrypted === '') {
            return '';
        }

        try {
            return $this->encryptor->decrypt($encrypted);
        } catch (\Throwable $exception) {
            $this->logger->error('Unable to decrypt MB Contact Form CAPTCHA key.', ['exception' => $exception]);
            return '';
        }
    }

    public function isCaptchaConfigured(?int $storeId = null): bool
    {
        return $this->getCaptchaProvider($storeId) === 'none'
            || ($this->getCaptchaSiteKey($storeId) !== '' && $this->getCaptchaSecretKey($storeId) !== '');
    }

    public function getCustomFields(?int $storeId = null): array
    {
        $raw = $this->scopeConfig->getValue(
            self::XML_PREFIX . 'custom_fields/rows',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_array($raw)) {
            $rows = $raw;
        } elseif (!is_string($raw) || trim($raw) === '') {
            return [];
        } else {
            try {
                $rows = $this->serializer->unserialize($raw);
            } catch (\InvalidArgumentException $exception) {
                $this->logger->error('Invalid MB Contact Form additional fields configuration.', ['exception' => $exception]);
                return [];
            }
        }

        $normalized = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $code = strtolower(trim((string)($row['code'] ?? '')));
            $label = trim((string)($row['label'] ?? ''));
            if (!preg_match('/^[a-z][a-z0-9_]{1,49}$/', $code) || $label === '' || isset($normalized[$code])) {
                continue;
            }

            $type = in_array(($row['type'] ?? ''), ['text', 'textarea', 'select'], true) ? (string)$row['type'] : 'text';
            $validation = in_array(($row['validation'] ?? ''), ['none', 'letters', 'alphanumeric', 'numbers', 'email', 'safe_text'], true)
                ? (string)$row['validation']
                : 'none';
            $options = array_values(array_unique(array_filter(
                array_map('trim', explode(',', (string)($row['options'] ?? ''))),
                'strlen'
            )));

            $normalized[$code] = [
                'code' => $code,
                'label' => $label,
                'type' => $type,
                'required' => (string)($row['required'] ?? '0') === '1',
                'validation' => $validation,
                'options' => $options,
                'sort_order' => (int)($row['sort_order'] ?? 0),
            ];
        }

        uasort($normalized, static fn(array $left, array $right): int => $left['sort_order'] <=> $right['sort_order']);
        return array_values($normalized);
    }

    private function getValue(string $path, ?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(
            self::XML_PREFIX . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        ));
    }
}
