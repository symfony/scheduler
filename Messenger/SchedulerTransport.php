<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Scheduler\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Message\RedispatchMessage;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Component\Scheduler\Exception\LogicException;
use Symfony\Component\Scheduler\Generator\MessageGeneratorInterface;

class SchedulerTransport implements TransportInterface
{
    public function __construct(
        private readonly MessageGeneratorInterface $messageGenerator,
    ) {
    }

    public function get(): iterable
    {
        foreach ($this->messageGenerator->getMessages() as $context => $message) {
            $stamp = new ScheduledStamp($context);

            if ($message instanceof RedispatchMessage) {
                $message = new RedispatchMessage(
                    Envelope::wrap($message->envelope, [$stamp]),
                    $message->transportNames,
                );
            }

            yield Envelope::wrap($message, [$stamp]);
        }
    }

    public function ack(Envelope $envelope): void
    {
        // ignore
    }

    public function reject(Envelope $envelope): void
    {
        // ignore
    }

    public function send(Envelope $envelope): Envelope
    {
        throw new LogicException(sprintf('"%s" cannot send messages.', __CLASS__));
    }

    public function getMessageGenerator(): MessageGeneratorInterface
    {
        return $this->messageGenerator;
    }
}
