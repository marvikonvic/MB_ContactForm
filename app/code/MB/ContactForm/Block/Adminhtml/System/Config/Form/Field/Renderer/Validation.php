<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer;

class Validation extends AbstractSelect
{
    protected function getOptionValues(): array
    {
        return [
            'none' => __('No additional validation'),
            'letters' => __('Latin and Cyrillic letters only'),
            'alphanumeric' => __('Latin and Cyrillic letters and numbers'),
            'numbers' => __('Numbers only'),
            'email' => __('Email Address'),
            'safe_text' => __('Latin and Cyrillic letters, numbers and punctuation'),
        ];
    }
}
