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

use Narrowspark\Automatic\Versions\Contract\Container as ContainerContract;
use Narrowspark\Automatic\Versions\Contract\InvalidArgumentException;
use function array_key_exists;
use function Safe\sprintf;

final class Container implements ContainerContract
{
    /**
     * The array of entries once they have been instantiated.
     *
     * @var array<string, mixed>
     */
    private array $objects = [];

    /**
     * The array of closures defining each entry of the container.
     *
     * @psalm-param callable[] $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $id, callable $callback): void
    {
        $this->data[$id] = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->objects)) {
            return $this->objects[$id];
        }

        if (! array_key_exists($id, $this->data)) {
            throw new InvalidArgumentException(sprintf('Identifier [%s] is not defined.', $id));
        }

        return $this->objects[$id] = $this->data[$id]($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return callable[]
     */
    public function getAll(): array
    {
        return $this->data;
    }
}
