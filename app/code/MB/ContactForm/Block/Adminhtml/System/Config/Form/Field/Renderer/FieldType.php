<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer;

class FieldType extends AbstractSelect
{
    protected function getOptionValues(): array
    {
        return [
            'text' => __('Text'),
            'textarea' => __('Textarea'),
            'select' => __('Select'),
        ];
    }
}
