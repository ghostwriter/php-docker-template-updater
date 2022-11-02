<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\XDebugEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;

final class XDebugListener extends AbstractListener
{
    public function __invoke(XDebugEvent $xDebugEvent): void
    {
        $from = $xDebugEvent->getFrom();
        $to = $xDebugEvent->getTo();

        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            $this->reset();

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

                    $commitMessage =  sprintf('[PHP %s]Bump Xdebug from %s to %s', $phpVersion, $from, $to);

                    $this->commit($commitMessage);
                    $this->pushAndMerge($commitMessage);
                }
            }
        }
    }
}
