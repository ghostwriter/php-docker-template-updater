<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event;

use Ghostwriter\EventDispatcher\AbstractEvent as AbstractEventDispatcherAbstractEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractEvent extends AbstractEventDispatcherAbstractEvent
{
    public function __construct(
        private InputInterface $input,
        private OutputInterface $output
    ) {
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
