<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Widget;

use MB\ContactForm\Model\Config;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Form extends Template implements BlockInterface
{
    protected $_template = 'MB_ContactForm::widget/form.phtml';

    private Config $config;
    private HttpContext $httpContext;

    public function __construct(
        Template\Context $context,
        Config $config,
        HttpContext $httpContext,
        array $data = []
    ) {
        $this->config = $config;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    protected function _toHtml(): string
    {
        if (!$this->config->isEnabled($this->getStoreId())
            || !$this->config->isAllowedForGroup($this->getCustomerGroupId(), $this->getStoreId())
            || !$this->config->isCaptchaConfigured($this->getStoreId())
        ) {
            return '';
        }

        return parent::_toHtml();
    }

    public function getFormId(): string
    {
        return 'mb-contact-' . substr(sha1($this->getNameInLayout()), 0, 10);
    }

    public function getFormTitle(): string
    {
        $widgetTitle = trim((string)$this->getData('title'));
        return $widgetTitle !== '' ? $widgetTitle : $this->config->getFormTitle($this->getStoreId());
    }

    public function getLabel(string $field): string
    {
        return $this->config->getLabel($field, $this->getStoreId());
    }

    public function getFields(): array
    {
        $fields = $this->config->getStandardFields($this->getStoreId());
        foreach ($this->getCustomFields() as $row) {
            $row['name'] = 'custom[' . $row['code'] . ']';
            $row['maxlength'] = $row['type'] === 'textarea' ? 1000 : 255;
            $fields[] = $row;
        }
        return $fields;
    }

    public function getCustomFields(): array
    {
        return $this->config->getCustomFields($this->getStoreId());
    }

    public function getPostUrl(): string
    {
        return $this->getUrl('mb_contactform/form/submit');
    }

    public function getCaptchaProvider(): string
    {
        return $this->config->getCaptchaProvider($this->getStoreId());
    }

    public function getCaptchaSiteKey(): string
    {
        return $this->config->getCaptchaSiteKey($this->getStoreId());
    }

    private function getCustomerGroupId(): int
    {
        return (int)$this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
    }
}
