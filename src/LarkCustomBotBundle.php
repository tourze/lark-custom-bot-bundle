<?php

namespace LarkCustomBotBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\HttpClientBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class LarkCustomBotBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
            DoctrineAsyncInsertBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
