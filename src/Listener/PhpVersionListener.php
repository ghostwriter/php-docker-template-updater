<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\PhpVersionEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

final class PhpVersionListener extends AbstractListener
{
    public function __invoke(PhpVersionEvent $event): void
    {
        $from = $event->getFrom();
        $to = $event->getTo();

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            foreach (PhpSAPI::SUPPORTED as $type) {
                $this->reset();

                $dockerFile = $this->dockerfilePath($phpVersion, $type);
                if (! is_file($dockerFile)) {
                    continue;
                }
                $format = 'php:%s-';
                $dockerFileContents = file_get_contents($dockerFile);
                if (str_contains($dockerFileContents, sprintf($format, $from))) {
                    $branchName = $this->branchName($phpVersion, 'php-' . $type, $from, $to);

                    $this->switch($branchName, ! $this->hasBranch($branchName));

                    file_put_contents(
                        $dockerFile,
                        str_replace(sprintf($format, $from), sprintf($format, $to), $dockerFileContents)
                    );

                    if ($this->hasChanges()) {
                        $this->add($dockerFile);

                        $commitMessage = sprintf(
                            '[PHP %s]Bump PHP-%s from %s to %s',
                            $phpVersion,
                            strtoupper($type),
                            $from,
                            $to
                        );

                        $this->commit($commitMessage);
                        $this->pushAndMerge($commitMessage);
                    }
                }
            }
        }
    }
}
