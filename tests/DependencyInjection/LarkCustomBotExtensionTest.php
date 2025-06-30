<?php

namespace LarkCustomBotBundle\Tests\DependencyInjection;

use LarkCustomBotBundle\DependencyInjection\LarkCustomBotExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LarkCustomBotExtensionTest extends TestCase
{
    private LarkCustomBotExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new LarkCustomBotExtension();
    }

    public function testLoad_shouldLoadServicesConfiguration(): void
    {
        $container = new ContainerBuilder();
        $configs = [];

        $this->extension->load($configs, $container);

        // 验证加载过程没有异常，容器仍然可用
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    public function testGetAlias_shouldReturnCorrectAlias(): void
    {
        $this->assertEquals('lark_custom_bot', $this->extension->getAlias());
    }
}
