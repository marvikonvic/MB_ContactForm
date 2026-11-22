<?php

declare(strict_types=1);

require dirname(__DIR__) . '/unit/bootstrap.php';

$module = dirname(__DIR__, 2) . '/app/code/MB/ContactForm';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($module));
$loaded = 0;
foreach ($files as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    if (str_contains($path, '/Test/')) {
        continue;
    }
    $source = file_get_contents($file->getPathname());
    if (preg_match('/^namespace ([^;]+);/m', $source, $namespace)
        && preg_match('/^(?:abstract )?class (\w+)/m', $source, $class)
    ) {
        $className = $namespace[1] . '\\' . $class[1];
        if (!class_exists($className)) {
            throw new RuntimeException('Cannot load module class: ' . $className);
        }
        $loaded++;
    }
}

// Verify the real core APIs used by the success page and email sender.
$policy = new Magento\Csp\Model\Policy\FetchPolicy('form-action', false, ['https://example.com']);
if ($policy->getHostSources() !== ['https://example.com']) {
    throw new RuntimeException('Incompatible CSP policy API.');
}
foreach (['setFromByScope', 'setReplyTo', 'getTransport'] as $method) {
    if (!method_exists(Magento\Framework\Mail\Template\TransportBuilder::class, $method)) {
        throw new RuntimeException('Missing TransportBuilder method: ' . $method);
    }
}

echo 'Loaded ' . $loaded . " module classes; CSP and mail contracts passed.\n";
