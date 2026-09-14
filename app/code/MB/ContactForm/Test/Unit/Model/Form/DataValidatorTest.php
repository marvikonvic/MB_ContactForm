<?php
declare(strict_types=1);

namespace MB\ContactForm\Test\Unit\Model\Form;

use Magento\Framework\Exception\LocalizedException;
use MB\ContactForm\Model\Config;
use MB\ContactForm\Model\Form\DataValidator;
use PHPUnit\Framework\TestCase;

final class DataValidatorTest extends TestCase
{
    private function subject(array $overrides = [], bool $custom = false): DataValidator
    {
        $field = array_replace([
            'code' => 'name', 'label' => 'Name', 'type' => 'text',
            'required' => true, 'maxlength' => 255, 'validation' => 'letters', 'options' => [],
        ], $overrides);
        $config = $this->createMock(Config::class);
        $config->method('getStandardFields')->with(2)->willReturn($custom ? [] : [$field]);
        $config->method('getCustomFields')->with(2)->willReturn($custom ? [$field] : []);
        return new DataValidator($config);
    }

    /** @dataProvider acceptedValues */
    public function testAcceptsAndTrimsValidInput(string $validation, string $value): void
    {
        $result = $this->subject(['validation' => $validation])->validate(['name' => ' ' . $value . ' '], 2);
        self::assertSame($value, $result['name']);
    }

    public static function acceptedValues(): array
    {
        return [
            ['letters', 'Željko Šarić'], ['letters', 'Бојан Петровић'],
            ['alphanumeric', 'Račun 123'], ['numbers', '00123'],
            ['email', 'bojan+test@example.com'], ['safe_text', "Zdravo!\nPoruka 123."],
        ];
    }

    /** @dataProvider rejectedValues */
    public function testRejectsInvalidInput(array $definition, $value, string $message): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage($message);
        $this->subject($definition)->validate(['name' => $value], 2);
    }

    public static function rejectedValues(): array
    {
        return [
            'required whitespace' => [[], '   ', 'required'],
            'array injection' => [[], ['value'], 'Invalid form field value'],
            'object input' => [[], new \stdClass(), 'Invalid form field value'],
            'name with digits' => [[], 'Bojan123', 'not allowed'],
            'emoji' => [[], 'Bojan😀', 'not allowed'],
            'invalid email' => [['validation' => 'email'], 'x@@example.com', 'not allowed'],
            'email newline injection' => [['validation' => 'email'], "x@example.com\nBcc: x@example.org", 'not allowed'],
            'negative number' => [['validation' => 'numbers'], '-123', 'not allowed'],
            'HTML' => [['validation' => 'safe_text'], '<script>alert(1)</script>', 'not allowed'],
            'too long' => [[], str_repeat('ž', 256), 'too long'],
            'forged select' => [['type' => 'select', 'options' => ['Support']], 'Other', 'not valid'],
        ];
    }

    public function testAcceptsExactMultibyteLengthLimit(): void
    {
        $value = str_repeat('ž', 255);
        self::assertSame($value, $this->subject()->validate(['name' => $value], 2)['name']);
    }

    public function testOptionalEmptyFieldAndUnknownFields(): void
    {
        $result = $this->subject(['required' => false])->validate(['injected' => 'value'], 2);
        self::assertSame('', $result['name']);
        self::assertArrayNotHasKey('injected', $result);
    }

    public function testAcceptsConfiguredSelectOption(): void
    {
        $result = $this->subject(['type' => 'select', 'options' => ['Support']])
            ->validate(['name' => 'Support'], 2);
        self::assertSame('Support', $result['name']);
    }

    public function testCustomFieldReturnsConfiguredLabelAndIgnoresUnknownFields(): void
    {
        $result = $this->subject([], true)->validate(['custom' => ['name' => ' Bojan ', 'other' => 'x']], 2);
        self::assertSame([['code' => 'name', 'label' => 'Name', 'value' => 'Bojan']], $result['custom']);
    }

    /** @dataProvider customInvalidValues */
    public function testRejectsInvalidCustomField(array $definition, $value): void
    {
        $this->expectException(LocalizedException::class);
        $this->subject($definition, true)->validate(['custom' => ['name' => $value]], 2);
    }

    public static function customInvalidValues(): array
    {
        return [
            [[], []], [[], ''], [[], str_repeat('a', 256)],
            [['type' => 'textarea'], str_repeat('a', 1001)],
            [['type' => 'select', 'options' => ['Support']], 'Other'],
        ];
    }

    public function testAcceptsCustomTextareaAtLimit(): void
    {
        $value = str_repeat('ž', 1000);
        $result = $this->subject(['type' => 'textarea'], true)->validate(['custom' => ['name' => $value]], 2);
        self::assertSame($value, $result['custom'][0]['value']);
    }
}
