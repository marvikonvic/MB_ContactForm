<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class WidgetCode extends Field
{
    private const WIDGET_CODE = '{{widget type="MB\\ContactForm\\Block\\Widget\\Form"}}';

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setValue(self::WIDGET_CODE);
        $element->setReadonly(true);
        $inputHtml = $element->getElementHtml();
        $buttonId = $element->getHtmlId() . '_copy';
        $init = $this->escapeHtmlAttr((string)json_encode([
            'MB_ContactForm/js/widget-code' => [
                'inputId' => $element->getHtmlId(),
                'copiedText' => (string)__('Copied'),
            ],
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT));

        return $inputHtml
            . '<button type="button" class="action-default" id="' . $this->escapeHtmlAttr($buttonId) . '"'
            . ' data-mage-init="' . $init . '"><span>' . $this->escapeHtml(__('Copy Widget Code')) . '</span></button>';
    }
}
