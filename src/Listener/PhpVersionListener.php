<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpSAPI;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use InvalidArgumentException;
use Throwable;

final class PhpVersionListener extends AbstractListener
{
    /**
     * @throws Throwable
     */
    public function __invoke(AbstractEvent $event): void
    {
        $input = $event->getInput();
        $output = $event->getOutput();

        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $dryRun = $input->getOption('dry-run');

        if (! is_string($from) || '' === $from) {
            throw new InvalidArgumentException('$from is invalid');
        }

        if (! is_string($to) || '' === $to) {
            throw new InvalidArgumentException('$to is invalid');
        }

        $git = $this->gitRepository->getWorkingCopy();
        $dir = $this->gitRepository->getWorkingDir();

        $git->checkout('main');
        foreach (PhpVersion::SUPPORTED as $phpVersion) {
            $phpVersionDir = sprintf('%s/%s', $dir, $phpVersion);
            if (! is_dir($phpVersionDir)) {
                continue;
            }

            foreach (PhpSAPI::SUPPORTED as $type) {
                $dockerFile = sprintf('%s/%s/Dockerfile', $phpVersionDir, $type);
                if (!is_file($dockerFile)) {
                    continue;
                }

                $dockerFileContents = file_get_contents($dockerFile);
                if (1 === preg_match(sprintf('#php:%s-#', $from), $dockerFileContents, $matches)) {
                    $output->writeln($phpVersion . ' dockerFile contents "' . $from . '" - ' . $dockerFile);
                    $branchName = sprintf('feature/php-%s/bump-php-%s-from-%s-to-%s', $phpVersion, $type, $from, $to);

                    if ($this->hasBranch($branchName)) {
                        $output->writeln('checkoutOldBranch - ' . $branchName);
                        $git->checkout($branchName);
                    } else {
                        $output->writeln('checkoutNewBranch - ' . $branchName);
                        $git->checkout('main', $branchName);
                    }

                    file_put_contents($dockerFile, str_replace($from, $to, $dockerFileContents));

                    if ($this->hasChanges()) {
                        $output->writeln('git:add - ' . $dockerFile);
                        $this->add($dockerFile);

                        $output->writeln('git:commit - ' . $branchName);
                        $this->commit([
                            '--message',
                            sprintf(
                                '[PHP %s]Bump PHP-%s from %s to %s',
                                $phpVersion,
                                strtoupper($type),
                                $from,
                                $to
                            ),
                            '--signoff',
                            '--gpg-sign',
                        ]);
                    }
                } else {
                    $output->writeln($phpVersion . ' dockerFile does not content "' . $from . '" - ' . $dockerFile);
                }
                $git->checkout('main');
            }
        }
    }
}
