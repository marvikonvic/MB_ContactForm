<?php
declare(strict_types=1);

namespace MB\ContactForm\Controller\Form;

use MB\ContactForm\Model\Captcha\Validator as CaptchaValidator;
use MB\ContactForm\Model\Config;
use MB\ContactForm\Model\Email\Sender;
use MB\ContactForm\Model\Form\DataValidator;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Submit extends Action implements HttpPostActionInterface
{
    private Config $config;
    private FormKeyValidator $formKeyValidator;
    private DataValidator $dataValidator;
    private CaptchaValidator $captchaValidator;
    private Sender $sender;
    private CustomerSession $customerSession;
    private StoreManagerInterface $storeManager;
    private LoggerInterface $logger;

    public function __construct(
        Context $context,
        Config $config,
        FormKeyValidator $formKeyValidator,
        DataValidator $dataValidator,
        CaptchaValidator $captchaValidator,
        Sender $sender,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->formKeyValidator = $formKeyValidator;
        $this->dataValidator = $dataValidator;
        $this->captchaValidator = $captchaValidator;
        $this->sender = $sender;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId = (int)$this->storeManager->getStore()->getId();
        $requestData = $this->getRequest()->getPostValue();

        try {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(__('Your session has expired. Please refresh the page and try again.'));
            }
            if (!$this->config->isEnabled($storeId)
                || !$this->config->isAllowedForGroup((int)$this->customerSession->getCustomerGroupId(), $storeId)
            ) {
                throw new LocalizedException(__('The contact form is not available.'));
            }
            if (!is_array($requestData)) {
                throw new LocalizedException(__('The submitted contact form data is not valid.'));
            }
            if (!$this->captchaValidator->validate($requestData, $storeId)) {
                throw new LocalizedException(__('CAPTCHA verification failed. Please try again.'));
            }

            $cleanData = $this->dataValidator->validate($requestData, $storeId);
            $this->sender->send($cleanData, $storeId);
            $customSuccessUrl = $this->config->getSuccessUrl($storeId);
            if ($customSuccessUrl === '') {
                $this->customerSession->setData('mb_contactform_email', $cleanData['email']);
            } else {
                $this->customerSession->unsetData('mb_contactform_email');
            }
            $this->messageManager->addSuccessMessage(__('Your message has been sent successfully.'));

            return $customSuccessUrl !== ''
                ? $resultRedirect->setUrl($customSuccessUrl)
                : $resultRedirect->setPath('mb_contactform/index/index');
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Throwable $exception) {
            $this->logger->critical('Unexpected MB Contact Form submission error.', ['exception' => $exception]);
            $this->messageManager->addErrorMessage(__('We could not send your message right now. Please try again later.'));
        }

        $referer = $this->_redirect->getRefererUrl();
        return $resultRedirect->setUrl($referer ?: $this->_url->getBaseUrl());
    }
}
