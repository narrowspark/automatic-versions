<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2021 Daniel Bannert
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/narrowspark/automatic-versions
 */

use Github\Client as GithubClient;
use Http\Client\Curl\Client as CurlClient;
use Narrowspark\Automatic\Versions\Builder;
use Narrowspark\Automatic\Versions\Container;
use Narrowspark\Automatic\Versions\Contract\Container as ContainerContract;
use Narrowspark\Automatic\Versions\Provider\GithubProvider;
use Narrowspark\Automatic\Versions\Provider\SymfonyProvider;
use Symfony\Component\Filesystem\Filesystem;

$composerFolder = __DIR__ . '/../vendor/autoload.php';

require_once $composerFolder;

$container = new Container([
    'root.path' => static fn (): string => dirname(__DIR__) . \DIRECTORY_SEPARATOR . '_site',
    'github.token' => static function (): string {
        $token = getenv('VERSIONS_GITHUB_TOKEN');

        if (! is_string($token)) {
            throw new RuntimeException('Please provide a github.com token.');
        }

        return $token;
    },
    GithubClient::class => static function (ContainerContract $container): GithubClient {
        $client = new GithubClient();
        $client->authenticate($container->get('github.token'), null, GithubClient::AUTH_ACCESS_TOKEN);

        return $client;
    },
    CurlClient::class => static fn (): CurlClient => new CurlClient(),
    Filesystem::class => static fn (): Filesystem => new Filesystem(),
    'github.illuminate.provider' => static fn (ContainerContract $container): GithubProvider => new GithubProvider($container->get(GithubClient::class), 'illuminate'),
    'github.viserio.provider' => static fn (ContainerContract $container): GithubProvider => new GithubProvider($container->get(GithubClient::class), 'viserio'),
    'github.symfony.provider' => static fn (ContainerContract $container): SymfonyProvider => new SymfonyProvider($container->get(CurlClient::class)),
    Builder::class => static function (ContainerContract $container): Builder {
        $builder = new Builder($container->get(Filesystem::class), $container->get('root.path'));

        $builder->setProvider('laravel/framework', $container->get('github.illuminate.provider'));
        $builder->setProvider('narrowspark/framework', $container->get('github.viserio.provider'));
        $builder->setProvider('symfony/symfony', $container->get('github.symfony.provider'));

        return $builder;
    },
]);

/** @var Builder $builder */
$builder = $container->get(Builder::class);

$builder->run();
