<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Exception\LocalizedException;

class CustomFields extends ArraySerialized
{
    private const TYPES = ['text', 'textarea', 'select'];
    private const VALIDATIONS = ['none', 'letters', 'alphanumeric', 'numbers', 'email', 'safe_text'];
    private const RESERVED_CODES = [
        'message', 'firstname', 'lastname', 'email', 'company', 'telephone',
        'form_key', 'g-recaptcha-response', 'cf-turnstile-response',
    ];

    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            $seen = [];

            foreach ($value as $rowId => $row) {
                if (!is_array($row)) {
                    continue;
                }

                $code = strtolower(trim((string)($row['code'] ?? '')));
                $label = trim((string)($row['label'] ?? ''));
                $type = (string)($row['type'] ?? 'text');
                $validation = (string)($row['validation'] ?? 'none');

                if (!preg_match('/^[a-z][a-z0-9_]{1,49}$/', $code)) {
                    throw new LocalizedException(
                        __('Each additional field code must contain 2-50 lowercase letters, numbers or underscores and start with a letter.')
                    );
                }
                if ($label === '') {
                    throw new LocalizedException(__('Every additional field must have a label.'));
                }
                if (isset($seen[$code]) || in_array($code, self::RESERVED_CODES, true)) {
                    throw new LocalizedException(__('The additional field code "%1" is duplicated or reserved.', $code));
                }
                if (!in_array($type, self::TYPES, true) || !in_array($validation, self::VALIDATIONS, true)) {
                    throw new LocalizedException(__('The additional field "%1" has an unsupported type or validation rule.', $label));
                }
                if ($type === 'select') {
                    $options = array_values(array_unique(array_filter(
                        array_map('trim', explode(',', (string)($row['options'] ?? ''))),
                        'strlen'
                    )));
                    if ($options === []) {
                        throw new LocalizedException(__('Select field "%1" must have comma-separated options.', $label));
                    }
                    $value[$rowId]['options'] = implode(', ', $options);
                }

                $seen[$code] = true;
            }
            $this->setValue($value);
        }

        return parent::beforeSave();
    }
}
