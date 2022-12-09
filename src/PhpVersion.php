<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater;

final class PhpVersion
{
    /**
     * @var string
     */
    public const PHP_56 = '5.6';

    /**
     * @var string
     */
    public const PHP_70 = '7.0';

    /**
     * @var string
     */
    public const PHP_71 = '7.1';

    /**
     * @var string
     */
    public const PHP_72 = '7.2';

    /**
     * @var string
     */
    public const PHP_73 = '7.3';

    /**
     * @var string
     */
    public const PHP_74 = '7.4';

    /**
     * @var string
     */
    public const PHP_80 = '8.0';

    /**
     * @var string
     */
    public const PHP_81 = '8.1';

    /**
     * @var string
     */
    public const PHP_82 = '8.2';

    /**
     * @var string
     */
    public const PHP_83 = '8.3';

    /**
     * @var string[]
     */
    public const SUPPORTED = [
        self::PHP_56,
        self::PHP_70,
        self::PHP_71,
        self::PHP_72,
        self::PHP_73,
        self::PHP_74,
        self::PHP_80,
        self::PHP_81,
        self::PHP_82,
        self::PHP_83,
    ];
}
