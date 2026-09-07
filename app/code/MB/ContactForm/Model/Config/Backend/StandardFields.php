<?php
declare(strict_types=1);
namespace MB\ContactForm\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\Exception\LocalizedException;

class StandardFields extends ArraySerialized
{
    public function beforeSave()
    {
        if (!is_array($this->getValue())) {
            throw new LocalizedException(__('Invalid standard field configuration.'));
        }
        $this->setValue((new \MB\ContactForm\Model\StandardFields())->normalize($this->getValue(), true));
        return parent::beforeSave();
    }
}
