<?php
declare(strict_types=1);

namespace MB\ContactForm\Block\Adminhtml\System\Config\Form\Field;

use MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer\FieldType;
use MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer\Required;
use MB\ContactForm\Block\Adminhtml\System\Config\Form\Field\Renderer\Validation;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class CustomFields extends AbstractFieldArray
{
    private ?FieldType $typeRenderer = null;
    private ?Required $requiredRenderer = null;
    private ?Validation $validationRenderer = null;

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function _prepareToRender(): void
    {
        $this->addColumn('code', ['label' => __('Code'), 'class' => 'required-entry']);
        $this->addColumn('label', ['label' => __('Label'), 'class' => 'required-entry']);
        $this->addColumn('type', ['label' => __('Type'), 'renderer' => $this->getTypeRenderer()]);
        $this->addColumn('required', ['label' => __('Required'), 'renderer' => $this->getRequiredRenderer()]);
        $this->addColumn('validation', ['label' => __('Validation'), 'renderer' => $this->getValidationRenderer()]);
        $this->addColumn('options', ['label' => __('Options (comma-separated)')]);
        $this->addColumn('sort_order', ['label' => __('Sort Order'), 'class' => 'validate-digits']);
        $this->_addAfter = false;
        $this->_addButtonLabel = (string)__('Add Field');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $options['option_' . $this->getTypeRenderer()->calcOptionHash((string)$row->getData('type'))] = 'selected="selected"';
        $options['option_' . $this->getRequiredRenderer()->calcOptionHash((string)$row->getData('required'))] = 'selected="selected"';
        $options['option_' . $this->getValidationRenderer()->calcOptionHash((string)$row->getData('validation'))] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }

    private function getTypeRenderer(): FieldType
    {
        if ($this->typeRenderer === null) {
            $this->typeRenderer = $this->getLayout()->createBlock(FieldType::class, '', ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->typeRenderer;
    }

    private function getRequiredRenderer(): Required
    {
        if ($this->requiredRenderer === null) {
            $this->requiredRenderer = $this->getLayout()->createBlock(Required::class, '', ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->requiredRenderer;
    }

    private function getValidationRenderer(): Validation
    {
        if ($this->validationRenderer === null) {
            $this->validationRenderer = $this->getLayout()->createBlock(Validation::class, '', ['data' => ['is_render_to_js_template' => true]]);
        }
        return $this->validationRenderer;
    }
}
