<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Tests\Unit;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Foo;

/**
 * @coversDefaultClass \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Foo
 *
 * @internal
 *
 * @small
 */
final class FooTest extends AbstractTestCase
{
    /** @covers \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Foo::test */
    public function test(): void
    {
        self::assertTrue((new Foo())->test());
    }
}
