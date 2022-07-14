<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Gitonomy\Git\Repository;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractListener
{
    public function __construct(
        protected Repository $gitRepository,
        protected SymfonyStyle $output
    )
    {
    }

    public function checkout(string $revision, string $branch = null): void
    {
        $checkoutBranch = $branch ?? $revision;
        $this->output->info('git:checkout - ' . $checkoutBranch);
        $this->gitRepository
            ->getWorkingCopy()
            ->checkout($revision, $branch);
    }

    public function add(string $filePattern): string
    {
        $this->output->info('git:add - ' . $filePattern);
        return $this->gitRepository->run('add', [$filePattern]);
    }

    public function commit(array $args): string
    {
        $this->output->info('git:commit - ' . json_encode($args));
        return $this->gitRepository->run('commit', [...$args]);
    }

    public function hasBranch(string $branchName): bool
    {
        return true === $this->gitRepository->getReferences()
            ->hasBranch($branchName);
    }

    public function hasChanges(): bool
    {
        return '' !== $this->gitRepository->run('status', ['-s']);
    }
}
