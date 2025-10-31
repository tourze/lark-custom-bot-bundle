<?php

declare(strict_types=1);
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Tourze\DoctrineAsyncInsertBundle\DoctrineAsyncInsertBundle;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;

return [
    FrameworkBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineFixturesBundle::class => ['all' => true],
    HttpClientBundle\HttpClientBundle::class => ['all' => true],
    DoctrineAsyncInsertBundle::class => ['all' => true],
    DoctrineIndexedBundle::class => ['all' => true],
    DoctrineTimestampBundle::class => ['all' => true],
    DoctrineTrackBundle::class => ['all' => true],
    LarkCustomBotBundle\LarkCustomBotBundle::class => ['all' => true],
];
