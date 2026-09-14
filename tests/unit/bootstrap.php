<?php
declare(strict_types=1);

// Point to an existing Magento Composer autoloader; no application bootstrap.
$autoload = getenv('MAGENTO_AUTOLOAD') ?: dirname(__DIR__, 2) . '/vendor/autoload.php';
if (!is_file($autoload)) {
    throw new RuntimeException('Set MAGENTO_AUTOLOAD to the Magento vendor/autoload.php path.');
}
require_once $autoload;
spl_autoload_register(static function (string $class): void {
    $prefix = 'MB\\ContactForm\\';
    if (strpos($class, $prefix) === 0) {
        $file = dirname(__DIR__, 2) . '/app/code/MB/ContactForm/'
            . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
}, true, true);
