<?php
declare(strict_types=1);

namespace MB\ContactForm\Block;

use MB\ContactForm\Model\Config;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;

class Success extends Template
{
    private Config $config;
    private CustomerSession $customerSession;
    private ?string $submittedEmail = null;

    public function __construct(
        Template\Context $context,
        Config $config,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->config = $config;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getSuccessMessage(): string
    {
        return $this->config->getSuccessMessage((int)$this->_storeManager->getStore()->getId());
    }

    public function getNewsletterMessage(): string
    {
        return $this->config->getNewsletterMessage((int)$this->_storeManager->getStore()->getId());
    }

    public function getNewsletterUrl(): string
    {
        return $this->config->getNewsletterUrl((int)$this->_storeManager->getStore()->getId());
    }

    public function getSubmittedEmail(): string
    {
        if ($this->submittedEmail === null) {
            $this->submittedEmail = trim((string)$this->customerSession->getData('mb_contactform_email'));
            $this->customerSession->unsetData('mb_contactform_email');
        }
        return $this->submittedEmail;
    }
}
