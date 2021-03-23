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

namespace Narrowspark\Automatic\Versions;

/** @noRector \Rector\CodingStyle\Rector\Use_\RemoveUnusedAliasRector */
use Narrowspark\Automatic\Versions\Contract\Provider as ProviderContract;
use Safe\DateTimeImmutable;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;

final class Builder
{
    /**
     * @psalm-var array<string, ProviderContract>
     */
    private array $providers;

    public function __construct(private Filesystem $filesystem, private string $rootPath)
    {
    }

    /**
     * @noRector
     */
    public function setProvider(string $name, ProviderContract $provider): void
    {
        /** @noRector */
        $this->providers[$name] = $provider;
    }

    public function run(): void
    {
        foreach ($this->providers as $name => $provider) {
            $orgPath = $this->rootPath . DIRECTORY_SEPARATOR . $name;

            if (! $this->filesystem->exists($orgPath)) {
                $this->filesystem->mkdir($orgPath);
            }

            echo "\nGenerating versions.json file for [{$name}].\n\n";

            $this->filesystem->dumpFile($orgPath . DIRECTORY_SEPARATOR . 'versions.json', Util::dumpJson(['splits' => $provider->fetch()]));
        }

        $datetime = (new DateTimeImmutable('now'))->format(DateTimeImmutable::RFC7231);

        $this->filesystem->dumpFile($this->rootPath . DIRECTORY_SEPARATOR . '_headers', "/*\n  Last-Modified: {$datetime}\n");
    }
}
