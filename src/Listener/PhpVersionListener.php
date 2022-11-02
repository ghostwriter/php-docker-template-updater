<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\PhpVersionEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

final class PhpVersionListener extends AbstractListener
{
    /**
     * @var string
     */
    private const FORMAT = 'php:%s-';
    public function __invoke(PhpVersionEvent $phpVersionEvent): void
    {
        $from = $phpVersionEvent->getFrom();
        $to = $phpVersionEvent->getTo();

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            foreach (PhpSAPI::SUPPORTED as $type) {
                $this->reset();

                $dockerFile = $this->dockerfilePath($phpVersion, $type);
                if (! is_file($dockerFile)) {
                    continue;
                }

                $dockerFileContents = file_get_contents($dockerFile);
                if (str_contains($dockerFileContents, sprintf(self::FORMAT, $from))) {
                    $branchName = $this->branchName($phpVersion, 'php-' . $type, $from, $to);

                    $this->switch($branchName, ! $this->hasBranch($branchName));

                    file_put_contents(
                        $dockerFile,
                        str_replace(sprintf(self::FORMAT, $from), sprintf(self::FORMAT, $to), $dockerFileContents)
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
