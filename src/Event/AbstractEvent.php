<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event;

use Ghostwriter\EventDispatcher\AbstractEvent as AbstractEventDispatcherAbstractEvent;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractEvent extends AbstractEventDispatcherAbstractEvent
{
    private string $from;

    private string $to;

    public function __construct(InputInterface $input)
    {
        $from = $input->getArgument('from');
        if (! is_string($from) || ! trim($from)) {
            throw new InvalidArgumentException('$from is empty');
        }

        $this->from = $from;

        $to = $input->getArgument('to');
        if (! is_string($to) || ! trim($to)) {
            throw new InvalidArgumentException('$to is empty');
        }

        $this->to = $to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }
}
