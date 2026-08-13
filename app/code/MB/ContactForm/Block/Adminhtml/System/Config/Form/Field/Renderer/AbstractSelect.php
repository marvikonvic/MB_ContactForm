<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

abstract class AbstractSelect extends Select
{
    private bool $optionsLoaded = false;

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    abstract protected function getOptionValues(): array;

    protected function _toHtml(): string
    {
        if (!$this->optionsLoaded) {
            foreach ($this->getOptionValues() as $value => $label) {
                $this->addOption($value, $label);
            }
            $this->optionsLoaded = true;
        }

        return parent::_toHtml();
    }

    public function setInputName(string $value): self
    {
        return $this->setName($value);
    }
}
