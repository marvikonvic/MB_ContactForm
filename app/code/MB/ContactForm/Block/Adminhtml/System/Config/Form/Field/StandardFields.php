<?php
declare(strict_types=1);
namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;
use MB\ContactForm\Model\StandardFields as Schema;

class StandardFields extends Field
{
    protected $_template = 'MB_ContactForm::system/config/standard-fields.phtml';
    private Json $json;
    private ScopeConfigInterface $scopeConfig;
    private array $rows = [];
    private string $inputName = '';

    public function __construct(Context $context, Json $json, ScopeConfigInterface $scopeConfig, array $data = [])
    {
        $this->json = $json;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(AbstractElement $element)
    {
        $schema = new Schema();
        $store = (int)$this->getRequest()->getParam('store');
        $website = (int)$this->getRequest()->getParam('website');
        $scope = $store ? 'stores' : ($website ? 'websites' : 'default');
        $scopeId = $store ?: $website;
        $labels = [];
        foreach (Schema::CODES as $code) {
            $labels[$code] = (string)$this->scopeConfig->getValue('mb_contactform/labels/' . $code, $scope, $scopeId);
        }
        $defaults = $schema->defaults(array_filter($labels, 'strlen'));
        $value = $element->getValue();
        if (is_string($value) && $value !== '') {
            $value = $this->json->unserialize($value);
        }
        $this->rows = is_array($value) ? $schema->normalize($value) : $defaults;
        foreach ($defaults as $code => $row) {
            if (!isset($this->rows[$code])) {
                $row['disabled'] = '1';
                $this->rows[$code] = $row;
            }
        }
        $this->inputName = $element->getName();
        return $this->_toHtml();
    }

    public function getRows(): array { return $this->rows; }
    public function getInputName(): string { return $this->inputName; }
}
