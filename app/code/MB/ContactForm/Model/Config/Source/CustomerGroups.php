<?php
declare(strict_types=1);

namespace MB\ContactForm\Model\Config\Source;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroups implements OptionSourceInterface
{
    private GroupRepositoryInterface $groupRepository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function toOptionArray(): array
    {
        $criteria = $this->searchCriteriaBuilder->create();
        $options = [];

        foreach ($this->groupRepository->getList($criteria)->getItems() as $group) {
            $options[] = [
                'value' => (string)$group->getId(),
                'label' => (string)$group->getCode(),
            ];
        }

        usort($options, static fn(array $left, array $right): int => (int)$left['value'] <=> (int)$right['value']);

        return $options;
    }
}
