<?php

declare(strict_types=1);

namespace Core;

use DI\ContainerBuilder;

final class Container
{
    /**
     * @param array<string, mixed> $definitions
     * @throws \Exception
     */
    public static function build(array $definitions = []): \DI\Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($definitions);

        /** @var \DI\Container $container */
        $container = $builder->build();

        return $container;
    }
}
