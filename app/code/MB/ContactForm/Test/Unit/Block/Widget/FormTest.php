<?php

declare(strict_types=1);

namespace MB\ContactForm\Test\Unit\Block\Widget;

use MB\ContactForm\Block\Widget\Form;
use MB\ContactForm\Model\StoreInformation;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /** @dataProvider storeIdProvider */
    #[DataProvider('storeIdProvider')]
    public function testStoreInformationResolvesStoreThroughManager(?int $storeId): void
    {
        $store = $this->createStub(Store::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->expects(self::once())->method('getStore')->with($storeId)->willReturn($store);
        $information = $this->createMock(StoreInformation::class);
        $information->expects(self::once())->method('getDetails')->with(self::identicalTo($store))
            ->willReturn(['name' => 'Current store']);

        // Keep the real widget methods, including Magento's magic data getters.
        $block = (new \ReflectionClass(Form::class))->newInstanceWithoutConstructor();
        $block->setData('store_id', $storeId);
        $managerProperty = new \ReflectionProperty(Template::class, '_storeManager');
        $managerProperty->setValue($block, $storeManager);
        $informationProperty = new \ReflectionProperty(Form::class, 'storeInformation');
        $informationProperty->setValue($block, $information);

        self::assertSame(['name' => 'Current store'], $block->getStoreInformation());
    }

    public static function storeIdProvider(): array
    {
        return ['current storefront' => [null], 'explicit widget store' => [7]];
    }
}
