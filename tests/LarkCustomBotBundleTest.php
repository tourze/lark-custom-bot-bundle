<?php

declare(strict_types=1);

namespace LarkCustomBotBundle\Tests;

use LarkCustomBotBundle\LarkCustomBotBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(LarkCustomBotBundle::class)]
#[RunTestsInSeparateProcesses]
final class LarkCustomBotBundleTest extends AbstractBundleTestCase
{
}
