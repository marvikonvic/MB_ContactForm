<?php
declare(strict_types=1);

namespace MB\ContactForm\Model;

use Magento\Store\Model\Information;
use Magento\Store\Model\Store;

class StoreInformation
{
    private Config $config;
    private Information $information;

    public function __construct(Config $config, Information $information)
    {
        $this->config = $config;
        $this->information = $information;
    }

    /**
     * Return plain text from the current store's native Store Information settings.
     *
     * @return array<string, string>
     */
    public function getDetails(Store $store): array
    {
        if (!$this->config->isStoreInformationEnabled((int)$store->getId())) {
            return [];
        }

        $information = $this->information->getStoreInformationObject($store);
        $address = [];
        foreach (['street_line1', 'street_line2', 'postcode', 'city', 'region', 'country'] as $field) {
            $value = trim((string)$information->getData($field));
            if ($value !== '') {
                $address[] = $value;
            }
        }

        $details = [
            'name' => trim((string)$information->getData('name')),
            'address' => implode("\n", $address),
            'phone' => trim((string)$information->getData('phone')),
            'hours' => trim((string)$information->getData('hours')),
            'vat_number' => trim((string)$information->getData('vat_number')),
        ];

        return array_filter($details, static fn (string $value): bool => $value !== '');
    }
}
