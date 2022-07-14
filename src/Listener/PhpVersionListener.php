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

        $this->reset();
        $this->checkout(self::BRANCH_MAIN);
        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            foreach (PhpSAPI::SUPPORTED as $type) {
                if (! $this->isBranch(self::BRANCH_MAIN)) {
                    $this->checkout(self::BRANCH_MAIN);
                }

                $dockerFile = sprintf('%s/%s/%s/Dockerfile', $this->gitRepository->getWorkingDir(), $phpVersion, $type);
                if (! is_file($dockerFile)) {
                    continue;
                }
                $format = 'php:%s-';
                $dockerFileContents = file_get_contents($dockerFile);
                if(str_contains($dockerFileContents, sprintf($format, $from))) {
                    $branchName = $this->branchName($phpVersion, 'php-' . $type, $from, $to);
                    
                    $this->hasBranch($branchName) ?
                        $this->checkout($branchName) :
                        $this->checkout(self::BRANCH_MAIN, $branchName);

                    file_put_contents(
                        $dockerFile,
                        str_replace(
                            sprintf($format, $from),
                            sprintf($format, $to),
                            $dockerFileContents
                        )
                    );

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
