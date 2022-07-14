<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater;

final class PhpSAPI
{
    /**
     * @var string
     */
    public const PHP_CLI = 'cli';

    /**
     * @var string
     */
    public const PHP_FPM = 'fpm';

    /**
     * @var string[]
     */
    public const SUPPORTED = [self::PHP_CLI, self::PHP_FPM];
}
