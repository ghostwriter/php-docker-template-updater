<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Gitonomy\Git\Repository;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractListener
{
    /**
     * @var string
     */
    protected const BRANCH_MAIN = 'main';

    public function __construct(
        protected Repository $gitRepository,
        protected SymfonyStyle $symfonyStyle
    ) {
    }

    public function add(string $filePattern): string
    {
        $this->symfonyStyle->info('git:add - ' . $filePattern);
        return $this->gitRepository->run('add', [$filePattern]);
    }

    public function checkout(string $revision, string $branch = null): void
    {
        is_string($branch) ?
            $this->symfonyStyle->info(sprintf('git:checkout - %s from %s', $branch, $revision)) :
            $this->symfonyStyle->info('git:checkout - ' . $revision);

        $this->gitRepository
            ->getWorkingCopy()
            ->checkout($revision, $branch);
    }

    public function commit(string $message): string
    {
        $this->symfonyStyle->info('git:commit - ' . $message);

        return $this->gitRepository->run('commit', ['--message', $message, '--signoff', '--gpg-sign']);
    }

    public function hasBranch(string $branchName): bool
    {
        return true === $this->gitRepository->getReferences()
            ->hasBranch($branchName);
    }

    public function hasChanges(): bool
    {
        return '' !== trim($this->gitRepository->run('status', ['-s']));
    }

    public function info(string $message): void
    {
        $this->symfonyStyle->info($message);
    }

    public function isBranch(string $branchName): bool
    {
        return trim($this->gitRepository->run('symbolic-ref', ['--short', 'HEAD'])) === $branchName;
    }
}
