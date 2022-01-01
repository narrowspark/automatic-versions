<?php

declare(strict_types=1);

/**
 * Copyright (c) 2020-2022 Daniel Bannert
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/narrowspark/automatic-versions
 */

namespace Narrowspark\Automatic\Versions;

/** @noRector \Rector\CodingStyle\Rector\Use_\RemoveUnusedAliasRector */
use Narrowspark\Automatic\Versions\Contract\Provider as ProviderContract;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Safe\DateTimeImmutable;
use Symfony\Component\Filesystem\Filesystem;
use const DIRECTORY_SEPARATOR;

final class Builder
{
    use LoggerAwareTrait;

    /**
     * @psalm-var array<string, ProviderContract>
     */
    private array $providers;

    public function __construct(private Filesystem $filesystem, private string $rootPath)
    {
        $this->logger = new NullLogger();
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

            $this->logger->info("\nGenerating versions.json file for [{$name}].\n\n");

            $this->filesystem->dumpFile($orgPath . DIRECTORY_SEPARATOR . 'versions.json', Util::dumpJson(['splits' => $provider->fetch()]));
        }

        $dateTimeImmutable = new DateTimeImmutable('now');
        $datetimeString = $dateTimeImmutable->format(DateTimeImmutable::RFC7231);

        $this->filesystem->dumpFile($this->rootPath . DIRECTORY_SEPARATOR . '_headers', "/*\n  Last-Modified: {$datetimeString}\n");
    }
}
