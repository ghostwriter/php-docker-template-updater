<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use Throwable;

final class ComposerListener extends AbstractListener
{
    /**
     * @throws Throwable
     */
    public function __invoke(AbstractEvent $event): void
    {
        $from = $event->getFrom();
        $to = $event->getTo();

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            if (! $this->isBranch(self::BRANCH_MAIN)) {
                $this->checkout(self::BRANCH_MAIN);
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

                $this->hasBranch($branchName) ?
                    $this->checkout($branchName) :
                    $this->checkout(self::BRANCH_MAIN, $branchName);

                file_put_contents($dockerFile, str_replace($formattedFrom, $formattedTo, $dockerFileContents));

                if ($this->hasChanges()) {
                    $this->add($dockerFile);
                    $this->commit(sprintf('[PHP %s]Bump composer/composer from %s to %s', $phpVersion, $from, $to));
                }
            }
        }
    }
}
