<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Email;

use MB\ContactForm\Model\Config;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Sender
{
    private Config $config;
    private TransportBuilder $transportBuilder;
    private StoreManagerInterface $storeManager;
    private LoggerInterface $logger;

    public function __construct(
        Config $config,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function send(array $data, int $storeId): void
    {
        $recipient = $this->config->getRecipientEmail($storeId);
        $senderEmail = $this->config->getSenderEmail($storeId);
        $senderName = $this->config->getSenderName($storeId);
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)
            || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)
            || $senderName === ''
        ) {
            throw new LocalizedException(__('The contact form email addresses are not configured correctly.'));
        }

        $customLines = [];
        foreach ($data['custom'] as $field) {
            if ($field['value'] !== '') {
                $customLines[] = (string)__($field['label']) . ': ' . $field['value'];
            }
        }

        $variables = $data + [
            'label_message' => (string)__($this->config->getLabel('message', $storeId)),
            'label_firstname' => (string)__($this->config->getLabel('firstname', $storeId)),
            'label_lastname' => (string)__($this->config->getLabel('lastname', $storeId)),
            'label_email' => (string)__($this->config->getLabel('email', $storeId)),
            'label_company' => (string)__($this->config->getLabel('company', $storeId)),
            'label_telephone' => (string)__($this->config->getLabel('telephone', $storeId)),
            'custom_fields_text' => implode("\n", $customLines),
            'store_name' => $this->storeManager->getStore($storeId)->getName(),
        ];

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->config->getEmailTemplate($storeId))
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars($variables)
                ->setFromByScope(['email' => $senderEmail, 'name' => $senderName], $storeId)
                ->addTo($recipient)
                ->setReplyTo($data['email'], trim($data['firstname'] . ' ' . $data['lastname']))
                ->getTransport();
            $transport->sendMessage();
        } catch (\Throwable $exception) {
            $this->logger->critical('Unable to send MB Contact Form email.', ['exception' => $exception]);
            throw new LocalizedException(__('We could not send your message right now. Please try again later.'));
        }
    }
}
