<?php

namespace LarkCustomBotBundle\Tests\DependencyInjection;

use LarkCustomBotBundle\DependencyInjection\LarkCustomBotExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(LarkCustomBotExtension::class)]
final class LarkCustomBotExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private LarkCustomBotExtension $extension;

    protected function setUp(): void
    {
        // 直接实例化扩展类
        // LarkCustomBotExtension 是一个扩展类，不是服务容器中的服务，直接实例化是正确的方式
        $this->extension = new LarkCustomBotExtension();
    }

    public function testGetAliasShouldReturnCorrectAlias(): void
    {
        $this->assertEquals('lark_custom_bot', $this->extension->getAlias());
    }
}
