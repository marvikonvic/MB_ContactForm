<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$root = dirname(__DIR__, 2);
$major = (int)explode('.', PHPUnit\Runner\Version::id())[0];
$configuration = $major >= 12 ? '/phpunit.xml.dist' : '/phpunit-9.xml.dist';
putenv('MAGENTO_AUTOLOAD=' . __DIR__ . '/autoload.php');

$commands = [
    [PHP_BINARY, __DIR__ . '/vendor/phpunit/phpunit/phpunit', '-c', $root . $configuration, '--colors=never'],
    [PHP_BINARY, __DIR__ . '/class-contracts.php'],
    [PHP_BINARY, $root . '/tests/standard-fields.php'],
];
if ($major >= 12) {
    array_push($commands[0], '--fail-on-phpunit-notice', '--fail-on-deprecation', '--fail-on-notice');
}
foreach ($commands as $command) {
    $process = proc_open($command, [STDIN, STDOUT, STDERR], $pipes, $root);
    if (!is_resource($process)) {
        throw new RuntimeException('Cannot start compatibility check.');
    }
    $status = proc_close($process);
    if ($status !== 0) {
        exit($status);
    }
}
