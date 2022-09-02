<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Tests\Unit;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\ComposerEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * @coversDefaultClass \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\ComposerEvent
 *
 * @internal
 *
 * @small
 */
final class ComposerEventTest extends AbstractTestCase
{
    /**
     * @covers \Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\ComposerEvent::__construct
     */
    public function testTrue(): void
    {
        self::assertInstanceOf(ComposerEvent::class, new ComposerEvent(
            new ArrayInput(
                [
                    'from' => '1.2.3',
                    'to' => '1.2.4',
                ],
                new InputDefinition([
                    new InputArgument('from', InputArgument::REQUIRED),
                    new InputArgument('to', InputArgument::REQUIRED),
                ])
            )
        ));
    }
}
