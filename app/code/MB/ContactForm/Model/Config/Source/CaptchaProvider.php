<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CaptchaProvider implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'none', 'label' => __('Disabled')],
            ['value' => 'google', 'label' => __('Google reCAPTCHA v2 Checkbox')],
            ['value' => 'turnstile', 'label' => __('Cloudflare Turnstile')],
        ];
    }
}
