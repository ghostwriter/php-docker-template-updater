<?php


declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Listener;

use Gitonomy\Git\Repository;

abstract class AbstractListener
{
    public function __construct(protected Repository $gitRepository)
    {
    }

    public function add(string $filePattern): string
    {
        return $this->gitRepository->run('add', [$filePattern]);
    }

    public function commit(array $args): string
    {
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
