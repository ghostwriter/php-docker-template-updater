<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Tests\Unit;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

/**
 * @coversDefaultClass \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion
 *
 * @internal
 * @small
 */
final class PhpVersionTest extends AbstractTestCase
{
    /**
     * @coversNothing
     */
    public function testTrue(): void
    {
        self::assertContains(PhpVersion::PHP_56, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_70, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_71, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_72, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_73, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_74, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_80, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_81, PhpVersion::SUPPORTED);
        self::assertContains(PhpVersion::PHP_82, PhpVersion::SUPPORTED);
    }
}
