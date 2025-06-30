<?php

namespace LarkCustomBotBundle\Tests\Unit;

use LarkCustomBotBundle\LarkCustomBotBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LarkCustomBotBundleTest extends TestCase
{
    private LarkCustomBotBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new LarkCustomBotBundle();
    }

    public function testBundleExtendsSymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testGetName_returnsCorrectBundleName(): void
    {
        $this->assertEquals('LarkCustomBotBundle', $this->bundle->getName());
    }

    public function testGetNamespace_returnsCorrectNamespace(): void
    {
        $this->assertEquals('LarkCustomBotBundle', $this->bundle->getNamespace());
    }

    public function testGetPath_returnsCorrectPath(): void
    {
        $expectedPath = dirname((new \ReflectionClass($this->bundle))->getFileName());
        
        $this->assertEquals($expectedPath, $this->bundle->getPath());
    }

    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new LarkCustomBotBundle();
        
        $this->assertInstanceOf(LarkCustomBotBundle::class, $bundle);
    }

    public function testGetContainerExtension_returnsExtensionOrNull(): void
    {
        $extension = $this->bundle->getContainerExtension();
        
        if ($extension !== null) {
            $this->assertEquals('lark_custom_bot', $extension->getAlias());
        }
    }
}