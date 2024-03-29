#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Ghostwriter\GhostwriterPhpDockerTemplateUpdater;

use Ghostwriter\Container\Container;
use Ghostwriter\Container\Contract\ContainerInterface;
use Ghostwriter\EventDispatcher\Contract\DispatcherInterface;
use Ghostwriter\EventDispatcher\Contract\ListenerProviderInterface;
use Ghostwriter\EventDispatcher\Dispatcher;
use Ghostwriter\EventDispatcher\ListenerProvider;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\ComposerEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\PhpExtensionEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\PhpVersionEvent;
use Ghostwriter\GhostwriterPhpDockerTemplateUpdater\Event\XDebugEvent;
use Gitonomy\Git\Repository;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Finder\Finder;
use function dirname;
use function sprintf;

/** @var ?string $_composer_autoload_path */
(static function (string $composerAutoloadPath): void {
    /** @psalm-suppress UnresolvableInclude */
    require $composerAutoloadPath ?: fwrite(
        STDERR,
        sprintf('[ERROR]Cannot locate "%s"\n please run "composer install"\n', $composerAutoloadPath)
    ) && exit(1);

    /**
     * #BlackLivesMatter.
     */

    $container = Container::getInstance();

    $container->alias(DispatcherInterface::class, Dispatcher::class);
    $container->alias(ListenerProviderInterface::class, ListenerProvider::class);

    $container->extend(
        ListenerProviderInterface::class,
        static function (
            ContainerInterface $container,
            ListenerProviderInterface $listenerProvider
        ): ListenerProviderInterface {
            $finder = $container->build(Finder::class)
                ->files()
                ->in(dirname(__DIR__) . '/src/Listener/')
                ->name('*Listener.php')
                ->notName('Abstract*.php')
                ->sortByName();

            foreach ($finder->getIterator() as $splFileInfo) {
                /** @var class-string $event */
                $event = sprintf('%s\Event\%sEvent', __NAMESPACE__, $splFileInfo->getBasename('Listener.php'));
                /** @var class-string $listener */
                $listener =  sprintf("%s\Listener\%s", __NAMESPACE__, $splFileInfo->getBasename('.php'));
                $listenerProvider->bindListener($event, $listener);
            }
            return $listenerProvider;
        }
    );

    $container->set(
        Repository::class,
        static fn (ContainerInterface $container): Repository =>
        $container->build(Repository::class, [
            'dir'=> getcwd(),
        ])
    );

    $container->build(SingleCommandApplication::class)
        ->setName('#BlackLivesMatter✊🏾')
        ->setVersion('1.0.0')
        ->setDescription('Updates versions of tools in the Dockerfiles.')
        ->addArgument('context', InputArgument::REQUIRED, 'The content to update.')
        ->addArgument('from', InputArgument::REQUIRED, 'The old version.')
        ->addArgument('to', InputArgument::REQUIRED, 'The new version.')
        ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simulate the update.')
        ->setCode(
            static function (InputInterface $input, OutputInterface $output) use ($container): int {
                $container->set(InputInterface::class, $input);
                $container->set(OutputInterface::class, $output);

                if ($container->get(Dispatcher::class)
                    ->dispatch(match ($input->getArgument('context')) {
                        'composer' => new ComposerEvent($input),
                        'ext' => new PhpExtensionEvent($input),
                        'php' => new PhpVersionEvent($input),
                        'xdebug' => new XDebugEvent($input),
                        default => throw new InvalidArgumentException()
                    })->isPropagationStopped()) {
                    return Command::FAILURE;
                }
                return Command::SUCCESS;
            }
        )
        ->run();
})($_composer_autoload_path ?? dirname(__DIR__) . '/vendor/autoload.php');
