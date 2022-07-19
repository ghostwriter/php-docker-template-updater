<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use Symfony\Component\Process\Process;
use Throwable;

final class PhpExtensionListener extends AbstractListener
{
    /**
     * @throws Throwable
     */
    public function __invoke(AbstractEvent $event): void
    {
        $extension = explode('-', $event->getFrom())[0];
        $from = explode('-', $event->getFrom())[1];
        $to = explode('-', $event->getTo())[1];

        $this->reset();
        $this->switch(self::BRANCH_MAIN);
        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            foreach (PhpSAPI::SUPPORTED as $type) {
                if (! $this->isBranch(self::BRANCH_MAIN)) {
                    $this->switch(self::BRANCH_MAIN);
                }

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
                        $this->commit(
                            sprintf(
                                '[PHP-%s %s]Bump `%s` extension from %s to %s',
                                strtoupper($type),
                                $phpVersion,
                                $extension,
                                $from,
                                $to
                            )
                        );

                        // $this->gitRepository->run('push');

                        // Process::fromShellCommandline('gh pr create --base "main" -f')->mustRun();
                        // Process::fromShellCommandline(sprintf('gh pr merge %s --merge', $branchName))->mustRun();
                    }
                }
            }
        }
    }
}
