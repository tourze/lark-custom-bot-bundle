<?php

namespace LarkCustomBotBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class LarkCustomBotExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
