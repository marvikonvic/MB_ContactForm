<?php
declare(strict_types=1);

namespace MB\ContactForm\Test\Unit\Model\Email;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use MB\ContactForm\Model\Config;
use MB\ContactForm\Model\Email\Sender;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class SenderTest extends TestCase
{
    private $config;
    private $builder;
    private $stores;
    private $logger;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->builder = $this->createMock(TransportBuilder::class);
        $this->stores = $this->createMock(StoreManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function subject(string $recipient = 'owner@example.com', string $sender = 'form@example.com', string $name = 'Contact'): Sender
    {
        $this->config->method('getRecipientEmail')->with(2)->willReturn($recipient);
        $this->config->method('getSenderEmail')->with(2)->willReturn($sender);
        $this->config->method('getSenderName')->with(2)->willReturn($name);
        $this->config->method('getEmailTemplate')->willReturn('mb_contactform_email_template');
        $this->config->method('getEmailSubject')->willReturn("Contact\r\n%store_name");
        $this->config->method('getEmailIntro')->willReturn('New message');
        $this->config->method('getLabel')->willReturn('Label');
        $this->config->method('getStandardFields')->willReturn([['code' => 'email', 'label' => 'Email']]);
        $store = $this->createMock(StoreInterface::class);
        $store->method('getName')->willReturn('Demo');
        $this->stores->method('getStore')->with(2)->willReturn($store);
        foreach (['setTemplateIdentifier', 'setTemplateOptions', 'setTemplateVars', 'setFromByScope', 'addTo', 'setReplyTo'] as $method) {
            $this->builder->method($method)->willReturnSelf();
        }
        return new Sender($this->config, $this->builder, $this->stores, $this->logger);
    }

    private function data(): array
    {
        return ['name' => ' Bojan ', 'email' => 'visitor@example.com', 'message' => 'Hello',
            'company' => '', 'telephone' => '', 'custom' => [
                ['label' => 'Topic', 'value' => 'Support'], ['label' => 'Empty', 'value' => ''],
            ]];
    }

    public function testSendsWithConfiguredSenderAndVisitorReplyTo(): void
    {
        $subject = $this->subject();
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects(self::once())->method('sendMessage');
        $this->builder->method('getTransport')->willReturn($transport);
        $this->builder->expects(self::once())->method('setFromByScope')
            ->with(['email' => 'form@example.com', 'name' => 'Contact'], 2);
        $this->builder->expects(self::once())->method('addTo')->with('owner@example.com');
        $this->builder->expects(self::once())->method('setReplyTo')->with('visitor@example.com', 'Bojan');
        $this->builder->expects(self::once())->method('setTemplateOptions')->with(['area' => 'frontend', 'store' => 2]);
        $this->builder->expects(self::once())->method('setTemplateVars')->with(self::callback(function (array $vars): bool {
            self::assertSame('Contact Demo', $vars['email_subject']);
            self::assertSame('Topic: Support', $vars['custom_fields_text']);
            self::assertSame('Hello', $vars['message']);
            self::assertTrue($vars['show_email']);
            self::assertFalse($vars['show_telephone']);
            return true;
        }));
        $this->logger->expects(self::never())->method('critical');
        $subject->send($this->data(), 2);
    }

    /** @dataProvider invalidConfiguration */
    public function testInvalidConfigurationFailsBeforeBuildingMail(string $recipient, string $sender, string $name): void
    {
        $subject = $this->subject($recipient, $sender, $name);
        $this->builder->expects(self::never())->method('getTransport');
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The contact form email addresses are not configured correctly.');
        $subject->send($this->data(), 2);
    }

    public static function invalidConfiguration(): array
    {
        return [['bad', 'form@example.com', 'Contact'], ['owner@example.com', 'bad', 'Contact'],
            ['owner@example.com', 'form@example.com', '']];
    }

    /** @dataProvider failureStages */
    public function testMailFailureIsLoggedAndExposesOnlySafeMessage(string $stage): void
    {
        $subject = $this->subject();
        $exception = new \RuntimeException('Sensitive SMTP detail');
        if ($stage === 'build') {
            $this->builder->method('getTransport')->willThrowException($exception);
        } else {
            $transport = $this->createMock(TransportInterface::class);
            $transport->method('sendMessage')->willThrowException($exception);
            $this->builder->method('getTransport')->willReturn($transport);
        }
        $this->logger->expects(self::once())->method('critical')->with(
            'Unable to send MB Contact Form email.', ['exception' => $exception]
        );
        try {
            $subject->send($this->data(), 2);
            self::fail('Mail failure must throw a LocalizedException.');
        } catch (LocalizedException $error) {
            self::assertSame('We could not send your message right now. Please try again later.', $error->getMessage());
        }
    }

    public static function failureStages(): array
    {
        return [['build'], ['send']];
    }
}
