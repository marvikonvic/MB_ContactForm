<?php
declare(strict_types=1);
namespace MB\ContactForm\Model;

use Magento\Framework\Exception\LocalizedException;

class StandardFields
{
    public const CODES = ['message', 'firstname', 'lastname', 'email', 'company', 'telephone'];
    public const REQUIRED = ['email', 'message'];
    public const LIMITS = ['message' => 5000, 'firstname' => 100, 'lastname' => 100,
        'email' => 254, 'company' => 150, 'telephone' => 30];

    public function defaults(array $labels = []): array
    {
        $names = ['Message', 'First Name', 'Last Name', 'Email Address', 'Company Name', 'Phone Number'];
        $rules = ['safe_text', 'letters', 'letters', 'email', 'alphanumeric', 'numbers'];
        $rows = [];
        foreach (self::CODES as $i => $code) {
            $rows[$code] = ['code' => $code, 'label' => $labels[$code] ?? $names[$i],
                'type' => $code === 'message' ? 'textarea' : 'text',
                'required' => $i < 4 ? '1' : '0', 'validation' => $rules[$i],
                'options' => '', 'sort_order' => ($i + 1) * 10, 'disabled' => '0'];
        }
        return $rows;
    }

    public function normalize(array $rows, bool $strict = false): array
    {
        unset($rows['__empty']);
        $result = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new LocalizedException(__('Invalid standard field configuration.'));
            }
            $code = (string)($row['code'] ?? '');
            if (!in_array($code, self::CODES, true) || isset($result[$code])) {
                throw new LocalizedException(__('Standard field codes must be unique and cannot be changed.'));
            }
            $label = trim((string)($row['label'] ?? ''));
            $type = (string)($row['type'] ?? 'text');
            $rule = (string)($row['validation'] ?? 'none');
            if ($label === '' || mb_strlen($label) > 255
                || !in_array($type, ['text', 'textarea', 'select'], true)
                || !in_array($rule, ['none', 'letters', 'alphanumeric', 'numbers', 'email', 'safe_text'], true)
            ) {
                throw new LocalizedException(__('Invalid standard field configuration.'));
            }
            $required = (string)($row['required'] ?? '0') === '1';
            $disabled = (string)($row['disabled'] ?? '0') === '1';
            if (in_array($code, self::REQUIRED, true)) {
                $expectedType = $code === 'email' ? 'text' : 'textarea';
                $expectedRule = $code === 'email' ? 'email' : 'safe_text';
                if ($strict && (!$required || $disabled || $type !== $expectedType || $rule !== $expectedRule)) {
                    throw new LocalizedException(__('Email and Message must remain enabled and required with their original type and validation.'));
                }
                $required = true;
                $disabled = false;
                $type = $expectedType;
                $rule = $expectedRule;
            }
            $options = array_values(array_unique(array_filter(array_map('trim',
                explode(',', (string)($row['options'] ?? ''))), 'strlen')));
            if ($type === 'select' && $options === []) {
                throw new LocalizedException(__('Select field "%1" must have comma-separated options.', $label));
            }
            $result[$code] = ['code' => $code, 'label' => $label, 'type' => $type,
                'required' => $required ? '1' : '0', 'disabled' => $disabled ? '1' : '0',
                'validation' => $rule, 'options' => implode(', ', $options),
                'sort_order' => (int)($row['sort_order'] ?? 0)];
        }
        foreach (self::REQUIRED as $code) {
            if (!isset($result[$code])) {
                if ($strict) {
                    throw new LocalizedException(__('Email and Message cannot be removed.'));
                }
                $result[$code] = $this->defaults()[$code];
            }
        }
        return $result;
    }
}
