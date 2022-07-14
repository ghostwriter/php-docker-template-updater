<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater;

final class PhpSAPI
{
    public const PHP_CLI = 'cli';

    public const PHP_FPM = 'fpm';

    public const SUPPORTED = [self::PHP_CLI, self::PHP_FPM];
}
