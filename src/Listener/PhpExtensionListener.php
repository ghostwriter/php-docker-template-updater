<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\PhpExtensionEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

final class PhpExtensionListener extends AbstractListener
{
    public function __invoke(PhpExtensionEvent $phpExtensionEvent): void
    {
        [$extension, $from] = explode('-', $phpExtensionEvent->getFrom());
        [, $to] = explode('-', $phpExtensionEvent->getTo());

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            foreach (PhpSAPI::SUPPORTED as $type) {
                $this->reset();

                $dockerFile = $this->dockerfilePath($phpVersion, $type);
                if (! is_file($dockerFile)) {
                    continue;
                }

                $dockerFileContents = file_get_contents($dockerFile);
                if (str_contains($dockerFileContents, $from)) {
                    $branchName = $this->branchName($type . '-' . $phpVersion, $extension, $from, $to);

                    $this->switch($branchName, ! $this->hasBranch($branchName));

                    file_put_contents($dockerFile, str_replace($from, $to, $dockerFileContents));

                    if ($this->hasChanges()) {
                        $this->add($dockerFile);

                        $commitMessage =  sprintf(
                            '[PHP-%s %s]Bump `%s` extension from %s to %s',
                            strtoupper($type),
                            $phpVersion,
                            $extension,
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
