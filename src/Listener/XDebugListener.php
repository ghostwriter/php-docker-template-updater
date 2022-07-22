<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use Throwable;

final class XDebugListener extends AbstractListener
{
    /**
     * @throws Throwable
     */
    public function __invoke(AbstractEvent $event): void
    {
        $from = $event->getFrom();
        $to = $event->getTo();

        $this->checkout(self::BRANCH_MAIN);
        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            if (! $this->isBranch(self::BRANCH_MAIN)) {
                $this->checkout(self::BRANCH_MAIN);
            }

            $dockerFile = $this->dockerfilePath($phpVersion);
            if (! is_file($dockerFile)) {
                continue;
            }

            $dockerFileContents = file_get_contents($dockerFile);
            if (1 === preg_match(sprintf('#XDEBUG_VERSION\s%s#', $from), $dockerFileContents)) {
                $branchName = $this->branchName($phpVersion, 'xdebug', $from, $to);

                $this->switch($branchName, ! $this->hasBranch($branchName));

                file_put_contents(
                    $dockerFile,
                    str_replace('_VERSION ' . $from, '_VERSION ' . $to, $dockerFileContents)
                );

                if ($this->hasChanges()) {
                    $this->add($dockerFile);
                    $this->commit(sprintf('[PHP %s]Bump Xdebug from %s to %s', $phpVersion, $from, $to));

                    // $this->pushAndMerge();
                }
            }
        }
    }
}
