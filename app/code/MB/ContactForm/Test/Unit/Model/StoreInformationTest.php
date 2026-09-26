<?php
declare(strict_types=1);

namespace MB\ContactForm\Test\Unit\Model;

use MB\ContactForm\Model\Config;
use MB\ContactForm\Model\StoreInformation;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Information;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class StoreInformationTest extends TestCase
{
    public function testDisabledDoesNotLoadStoreInformation(): void
    {
        $config = $this->createMock(Config::class);
        $config->expects(self::once())->method('isStoreInformationEnabled')->with(7)->willReturn(false);
        $information = $this->createMock(Information::class);
        $information->expects(self::never())->method('getStoreInformationObject');
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(7);

        self::assertSame([], (new StoreInformation($config, $information))->getDetails($store));
    }

    /** @dataProvider detailsProvider */
    public function testEnabledUsesCurrentStoreAndOmitsEmptyValues(array $data, array $expected): void
    {
        $config = $this->createMock(Config::class);
        $config->expects(self::once())->method('isStoreInformationEnabled')->with(12)->willReturn(true);
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(12);
        $information = $this->createMock(Information::class);
        $information->expects(self::once())->method('getStoreInformationObject')
            ->with(self::identicalTo($store))->willReturn(new DataObject($data));

        self::assertSame($expected, (new StoreInformation($config, $information))->getDetails($store));
    }

    public static function detailsProvider(): array
    {
        return [
            'empty store' => [[], []],
            'whitespace and zero' => [
                ['name' => '  ', 'phone' => ' 0 ', 'hours' => "\n", 'street_line1' => ' '],
                ['phone' => '0'],
            ],
            'all native fields' => [
                [
                    'name' => ' Store ', 'street_line1' => ' Main 1 ', 'street_line2' => ' Floor 2 ',
                    'postcode' => '11000', 'city' => 'Belgrade', 'region' => 'Belgrade Region',
                    'country' => 'Serbia', 'phone' => '+381 11 123456', 'hours' => "Mon-Fri 9-17\nSat 9-12",
                    'vat_number' => '123456789',
                ],
                [
                    'name' => 'Store', 'address' => "Main 1\nFloor 2\n11000\nBelgrade\nBelgrade Region\nSerbia",
                    'phone' => '+381 11 123456', 'hours' => "Mon-Fri 9-17\nSat 9-12", 'vat_number' => '123456789',
                ],
            ],
            'partial address' => [
                ['street_line1' => 'Main 1', 'street_line2' => null, 'city' => 'Belgrade'],
                ['address' => "Main 1\nBelgrade"],
            ],
        ];
    }

    public function testEmptyDetailsKeepOriginalFormStructure(): void
    {
        $html = $this->renderTemplate([]);
        self::assertStringNotContainsString('class="mb-contact__layout"', $html);
        self::assertStringNotContainsString('<aside', $html);
        self::assertSame(1, substr_count($html, '<form '));
    }

    public function testPanelEscapesConfiguredMarkupAndRemainsOutsideForm(): void
    {
        $html = $this->renderTemplate(['name' => '<script>alert(1)</script>', 'hours' => "Monday\nTuesday"]);
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        self::assertStringContainsString("Monday\nTuesday", $html);
        $document = new \DOMDocument();
        @$document->loadHTML($html);
        $xpath = new \DOMXPath($document);
        self::assertSame(0, $xpath->query('//form//aside')->length);
        self::assertSame(1, $xpath->query('//div[@class="mb-contact__layout"]/aside')->length);
        self::assertSame(1, $xpath->query('//h3[@id="test-form-store-information-title"]')->length);
    }

    private function renderTemplate(array $details): string
    {
        $block = new class ($details) {
            private array $details;
            public function __construct(array $details) { $this->details = $details; }
            public function getFormId(): string { return 'test-form'; }
            public function getCaptchaProvider(): string { return 'none'; }
            public function getStoreInformation(): array { return $this->details; }
            public function getFormTitle(): string { return 'Contact'; }
            public function getPostUrl(): string { return '/contact-success/form/submit/'; }
            public function getBlockHtml(string $name): string { return ''; }
            public function getFields(): array { return []; }
            public function getLabel(string $field): string { return 'Submit'; }
        };
        // A deterministic escaping double keeps this template test independent of Magento bootstrap.
        $escaper = new class {
            public function escapeHtml($value): string
            {
                return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            }
            public function escapeHtmlAttr($value): string { return $this->escapeHtml($value); }
            public function escapeUrl($value): string { return $this->escapeHtml($value); }
        };
        ob_start();
        try {
            include dirname(__DIR__, 3) . '/view/frontend/templates/widget/form.phtml';
            return (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    public function testToggleUsesStoreScope(): void
    {
        $scope = $this->createMock(ScopeConfigInterface::class);
        $scope->expects(self::once())->method('isSetFlag')
            ->with('mb_contactform/general/enable_store_information', ScopeInterface::SCOPE_STORE, 12)
            ->willReturn(true);
        $config = new Config(
            $scope,
            $this->createMock(EncryptorInterface::class),
            new Json(),
            $this->createMock(LoggerInterface::class)
        );

        self::assertTrue($config->isStoreInformationEnabled(12));
    }
}
