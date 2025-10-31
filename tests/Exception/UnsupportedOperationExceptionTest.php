<?php

namespace LarkCustomBotBundle\Tests\Exception;

use LarkCustomBotBundle\Exception\UnsupportedOperationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(UnsupportedOperationException::class)]
final class UnsupportedOperationExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return UnsupportedOperationException::class;
    }

    protected function getDefaultMessage(): string
    {
        return '';
    }

    protected function getDefaultCode(): int
    {
        return 0;
    }
}
