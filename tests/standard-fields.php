<?php
declare(strict_types=1);
// Standalone contract tests with lightweight Magento service doubles.
namespace Magento\Framework\Exception { class LocalizedException extends \Exception {} }
namespace Magento\Framework\App\Config { interface ScopeConfigInterface {} }
namespace Magento\Framework\Encryption { interface EncryptorInterface {} }
namespace Psr\Log { interface LoggerInterface {} }
namespace Magento\Store\Model { class ScopeInterface { public const SCOPE_STORE = 'stores'; } }
namespace Magento\Framework\Serialize\Serializer {
    class Json { public function unserialize($value) { return json_decode($value, true, 512, JSON_THROW_ON_ERROR); } }
}
namespace {
    function __($message, ...$args) {
        foreach ($args as $i => $arg) { $message = str_replace('%' . ($i + 1), (string)$arg, $message); }
        return $message;
    }
    $root = dirname(__DIR__) . '/app/code/MB/ContactForm/';
    require $root . 'Model/StandardFields.php';
    require $root . 'Model/Config.php';
    require $root . 'Model/Form/DataValidator.php';
    $checks = 0;
    function check(bool $condition, string $message): void {
        global $checks;
        if (!$condition) { throw new \RuntimeException($message); }
        $checks++;
    }
    function rejected(callable $operation, string $message): void {
        try { $operation(); } catch (\Magento\Framework\Exception\LocalizedException $e) { check(true, $message); return; }
        check(false, $message);
    }
    $schema = new \MB\ContactForm\Model\StandardFields();
    $rows = $schema->defaults();
    check(count($schema->normalize($rows, true)) === 6, 'Default fields');
    foreach (['email', 'message'] as $code) {
        $bad = $rows; unset($bad[$code]);
        rejected(fn() => $schema->normalize($bad, true), 'Cannot remove protected field');
        foreach (['required' => '0', 'disabled' => '1', 'type' => 'select', 'validation' => 'none'] as $key => $value) {
            $bad = $rows; $bad[$code][$key] = $value;
            rejected(fn() => $schema->normalize($bad, true), 'Protected contract ' . $key);
        }
    }
    $bad = $rows; $bad['email']['code'] = 'other';
    rejected(fn() => $schema->normalize($bad, true), 'Unknown code rejected');
    $bad = $rows; $bad['company']['type'] = 'select';
    rejected(fn() => $schema->normalize($bad, true), 'Select requires options');
    $scope = new class implements \Magento\Framework\App\Config\ScopeConfigInterface {
        public array $values = [];
        public function getValue($path, $scope = null, $id = null) { return $this->values[$id][$path] ?? null; }
    };
    $logger = new class implements \Psr\Log\LoggerInterface { public function error(...$args) {} };
    $encryptor = new class implements \Magento\Framework\Encryption\EncryptorInterface {};
    $config = new \MB\ContactForm\Model\Config($scope, $encryptor, new \Magento\Framework\Serialize\Serializer\Json(), $logger);
    $scope->values[1]['mb_contactform/labels/firstname'] = 'Ime';
    check($config->getStandardFieldRows(1)['firstname']['label'] === 'Ime', 'Legacy Store View label retained');
    check($config->getStandardFieldRows(2)['firstname']['label'] === 'First Name', 'Store labels isolated');
    $rows['company']['disabled'] = '1';
    $rows['firstname']['required'] = '0';
    $rows['lastname']['disabled'] = '1';
    $rows['message']['sort_order'] = 999;
    $scope->values[1]['mb_contactform/labels/rows'] = json_encode($rows);
    $fields = $config->getStandardFields(1);
    check(end($fields)['code'] === 'message', 'Frontend order follows configured sort');
    check(!in_array('company', array_column($fields, 'code')), 'Disabled field omitted');
    $validator = new \MB\ContactForm\Model\Form\DataValidator($config);
    $data = ['email' => 'test@example.com', 'message' => 'Hello world!', 'company' => '<script>ignored</script>'];
    $clean = $validator->validate($data, 1);
    check($clean['company'] === '' && $clean['lastname'] === '', 'Disabled values discarded');
    check($clean['firstname'] === '', 'Optional name can be empty');
    rejected(fn() => $validator->validate(['email' => 'bad', 'message' => 'Hello'], 1), 'Email validated');
    rejected(fn() => $validator->validate(['email' => 'test@example.com'], 1), 'Message required');
    rejected(fn() => $validator->validate(['email' => ['bad'], 'message' => 'Hello'], 1), 'Non-scalar rejected');
    $rows['telephone']['type'] = 'select'; $rows['telephone']['options'] = '123, 456';
    $scope->values[1]['mb_contactform/labels/rows'] = json_encode($rows);
    rejected(fn() => $validator->validate($data + ['telephone' => '789'], 1), 'Select allowlist checked');
    check($validator->validate($data + ['telephone' => '123'], 1)['telephone'] === '123', 'Valid select accepted');
    $email = file_get_contents($root . 'view/frontend/email/contact_form_notification.html');
    $last = -1;
    foreach (['firstname', 'lastname', 'email', 'company', 'telephone', 'message'] as $code) {
        $position = strpos($email, '<p><strong>{{var label_' . $code);
        check($position > $last, 'Email order ' . $code); $last = $position;
    }
    echo "PASS: $checks contract checks\n";
}
