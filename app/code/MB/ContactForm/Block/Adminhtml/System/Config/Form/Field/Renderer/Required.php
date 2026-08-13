<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer;

class Required extends AbstractSelect
{
    protected function getOptionValues(): array
    {
        return [
            '0' => __('No'),
            '1' => __('Yes'),
        ];
    }
}
