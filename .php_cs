<?php

declare(strict_types=1);

use Ergebnis\License;
use Narrowspark\CS\Config\Config;

$license = License\Type\MIT::markdown(
    __DIR__ . '/LICENSE.md',
    License\Range::since(
        License\Year::fromString('2020'),
        new \DateTimeZone('UTC')
    ),
    License\Holder::fromString('Daniel Bannert'),
    License\Url::fromString('https://github.com/narrowspark/automatic-versions')
);

$license->save();

$config = new Config($license->header(), []);

$config->getFinder()
    ->files()
    ->in(__DIR__)
    ->exclude([
        '.build',
        '.docker',
        '.github',
        'vendor'
    ])
    // php_unit_namespaced rule thinks than the const are some namespaces
    ->notPath('rector.php')
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config->setCacheFile(__DIR__ . '/.build/php-cs-fixer/.php_cs.cache');

return $config;
