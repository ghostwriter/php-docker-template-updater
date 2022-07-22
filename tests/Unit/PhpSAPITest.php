<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Tests\Unit;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;

/**
 * @coversDefaultClass \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI
 *
 * @internal
 * @small
 */
final class PhpSAPITest extends AbstractTestCase
{
    /**
     * @coversNothing
     */
    public function testTrue(): void
    {
        self::assertContains(PhpSAPI::PHP_CLI, PhpSAPI::SUPPORTED);
        self::assertContains(PhpSAPI::PHP_FPM, PhpSAPI::SUPPORTED);
    }
}
