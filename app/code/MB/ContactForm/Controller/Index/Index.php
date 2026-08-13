<?php
declare(strict_types=1);

namespace MB\ContactForm\Controller\Index;

use MB\ContactForm\Model\Config;
use Magento\Csp\Api\CspAwareActionInterface;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

class Index extends Action implements HttpGetActionInterface, CspAwareActionInterface
{
    private PageFactory $pageFactory;
    private Config $config;
    private StoreManagerInterface $storeManager;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->set((string)__($this->config->getSuccessMetaTitle($storeId)));
        $page->getConfig()->setDescription((string)__($this->config->getSuccessMetaDescription($storeId)));
        $page->getConfig()->setRobots('NOINDEX,FOLLOW');
        return $page;
    }

    /**
     * Add the configured newsletter endpoint to form-action only on the success page.
     *
     * @param array $appliedPolicies
     * @return array
     */
    public function modifyCsp(array $appliedPolicies): array
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $origin = $this->getNewsletterOrigin($this->config->getNewsletterUrl($storeId));
        if ($origin !== null) {
            $appliedPolicies[] = new FetchPolicy('form-action', false, [$origin]);
        }

        return $appliedPolicies;
    }

    private function getNewsletterOrigin(string $url): ?string
    {
        $parts = parse_url($url);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        $scheme = strtolower((string)$parts['scheme']);
        if (!in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $origin = $scheme . '://' . strtolower((string)$parts['host']);
        if (isset($parts['port'])) {
            $origin .= ':' . (int)$parts['port'];
        }

        return $origin;
    }
}
