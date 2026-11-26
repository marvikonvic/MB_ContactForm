<?php

declare(strict_types=1);

$magentoSource = getenv('MAGENTO_SOURCE');
if (!$magentoSource || !is_dir($magentoSource)) {
    throw new RuntimeException('Set MAGENTO_SOURCE to an original Magento source checkout.');
}
$magentoSource = realpath($magentoSource);
require __DIR__ . '/vendor/autoload.php';

spl_autoload_register(static function (string $class) use ($magentoSource): void {
    $prefix = 'Magento\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    [$module, $relativeClass] = explode('\\', substr($class, strlen($prefix)), 2);
    $directory = $module === 'Framework'
        ? '/lib/internal/Magento/Framework/'
        : '/app/code/Magento/' . $module . '/';
    $file = $magentoSource . $directory . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

require_once $magentoSource . '/lib/internal/Magento/Framework/Phrase/__.php';
