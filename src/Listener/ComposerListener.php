<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\ComposerEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

final class ComposerListener extends AbstractListener
{
    public function __invoke(ComposerEvent $event): void
    {
        $from = $event->getFrom();
        $to = $event->getTo();

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            if (! $this->isBranch(self::BRANCH_MAIN)) {
                $this->reset();
            }

            $dockerFile = $this->dockerfilePath($phpVersion, 'composer');
            if (! is_file($dockerFile)) {
                continue;
            }

            $format = '_VERSION %s';
            $formattedFrom = sprintf($format, $from);
            $formattedTo = sprintf($format, $to);

            $dockerFileContents = file_get_contents($dockerFile);
            if (str_contains($dockerFileContents, $formattedFrom)) {
                $branchName = $this->branchName($phpVersion, 'composer', $from, $to);

                $this->switch($branchName, ! $this->hasBranch($branchName));

                file_put_contents($dockerFile, str_replace($formattedFrom, $formattedTo, $dockerFileContents));

                if ($this->hasChanges()) {
                    $this->add($dockerFile);

                    $commitMessage = sprintf('[PHP %s]Bump composer/composer from %s to %s', $phpVersion, $from, $to);

                    $this->commit($commitMessage);
                    $this->pushAndMerge($commitMessage);
                }
            }
        }
    }
}
