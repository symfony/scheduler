<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Scheduler\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Scheduler\DependencyInjection\AddScheduleMessengerPass;

class AddScheduleMessengerPassTest extends TestCase
{
    /**
     * @dataProvider processSchedulerTaskCommandProvider
     */
    public function testProcessSchedulerTaskCommand(array $arguments, string $expectedCommand)
    {
        $container = new ContainerBuilder();

        $definition = new Definition(SchedulableCommand::class);
        $definition->addTag('console.command');
        $definition->addTag('scheduler.task', $arguments);
        $container->setDefinition(SchedulableCommand::class, $definition);

        (new AddScheduleMessengerPass())->process($container);

        $schedulerProvider = $container->getDefinition('scheduler.provider.default');
        $calls = $schedulerProvider->getMethodCalls();

        $this->assertCount(1, $calls);
        $this->assertCount(2, $calls[0]);

        $messageDefinition = $calls[0][1][0];
        $messageArguments = $messageDefinition->getArgument('$message');
        $command = $messageArguments->getArgument(0);

        $this->assertSame($expectedCommand, $command);
    }

    public static function processSchedulerTaskCommandProvider(): iterable
    {
        yield 'no arguments' => [['trigger' => 'every', 'frequency' => '1 hour'], 'schedulable'];
        yield 'null arguments' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => null], 'schedulable'];
        yield 'empty arguments' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => ''], 'schedulable'];
        yield 'test argument' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => 'test'], 'schedulable test'];
        yield 'array arguments' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => ['arg1', 'arg2']], 'schedulable arg1 arg2'];
        yield 'array arguments with spaces' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => ['hello world', 'foo']], 'schedulable '.escapeshellarg('hello world').' foo'];
        yield 'empty array arguments' => [['trigger' => 'every', 'frequency' => '1 hour', 'arguments' => []], 'schedulable'];
    }
}

#[AsCommand(name: 'schedulable')]
class SchedulableCommand
{
    public function __invoke(): void
    {
    }
}
