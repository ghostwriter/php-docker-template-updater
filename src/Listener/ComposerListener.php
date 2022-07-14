<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\AbstractEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\PhpVersion;
use InvalidArgumentException;
use Throwable;

final class ComposerListener extends AbstractListener
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
        // TODO: Should i add a `--dry-run` option?
        // $dryRun = $input->getOption('dry-run');


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

            $dockerFile = $phpVersionDir . '/composer/Dockerfile';
            if (! is_file($dockerFile)) {
                continue;
            }

            $dockerFileContents = file_get_contents($dockerFile);

            if (1 === preg_match(sprintf('#_VERSION\s%s#', $from), $dockerFileContents, $matches)) {
                $branchName = 'feature/php-' . $phpVersion . '/bump-composer-from-' . $from . '-to-' . $to;

                if ($this->hasBranch($branchName)) {
                    $output->writeln('checkoutOldBranch - ' . $branchName);
                    $git->checkout($branchName);
                } else {
                    $output->writeln('checkoutNewBranch - ' . $branchName);
                    $git->checkout('main', $branchName);
                }

                file_put_contents(
                    $dockerFile,
                    str_replace('_VERSION ' . $from, '_VERSION ' . $to, $dockerFileContents)
                );

                if ($this->hasChanges()) {
                    $this->add($dockerFile);
                    $output->writeln('git:add - ' . $dockerFile);

                    $this->commit([
                        '--message',
                        sprintf('[PHP %s]Bump composer/composer from %s to %s', $phpVersion, $from, $to),
                        '--signoff',
                        '--gpg-sign',
                    ]);

                    $output->writeln('git:commit - ' . $branchName);
                }
            } else {
                $output->writeln($phpVersion . ' dockerFile does not content "' . $from . '" - ' . $dockerFile);
            }

            $git->checkout('main');
        }
    }
}
