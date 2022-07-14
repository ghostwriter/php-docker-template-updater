<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use Throwable;

final class PhpVersionListener extends AbstractListener
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
            $phpVersionDir = sprintf('%s/%s', $this->gitRepository->getWorkingDir(), $phpVersion);
            if (! is_dir($phpVersionDir)) {
                continue;
            }

            foreach (PhpSAPI::SUPPORTED as $type) {
                if (! $this->isBranch(self::BRANCH_MAIN)) {
                    $this->checkout(self::BRANCH_MAIN);
                }

                $dockerFile = sprintf('%s/%s/Dockerfile', $phpVersionDir, $type);
                if (! is_file($dockerFile)) {
                    continue;
                }

                $dockerFileContents = file_get_contents($dockerFile);
                if (1 === preg_match(sprintf('#php:%s-#', $from), $dockerFileContents)) {
                    $branchName = sprintf('feature/php-%s/bump-php-%s-from-%s-to-%s', $phpVersion, $type, $from, $to);

                    $this->hasBranch($branchName) ?
                        $this->checkout($branchName) :
                        $this->checkout(self::BRANCH_MAIN, $branchName);

                    file_put_contents($dockerFile, str_replace($from, $to, $dockerFileContents));

                    if ($this->hasChanges()) {
                        $this->add($dockerFile);
                        $this->commit(
                            sprintf('[PHP %s]Bump PHP-%s from %s to %s', $phpVersion, strtoupper($type), $from, $to)
                        );
                    }
                }
            }
        }
    }
}
