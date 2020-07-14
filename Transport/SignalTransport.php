<?php

declare(strict_types=1);

namespace SecIT\SignalNotifierBundle\Transport;

use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Exception\RuntimeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SignalTransport extends AbstractTransport
{
    private string $cliPath;
    private string $user;

    public function __construct(
        string $cliPath,
        string $user,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->cliPath = $cliPath;
        $this->user = $user;

        parent::__construct(null, $dispatcher);
    }

    public function __toString(): string
    {
        return sprintf(
            'signal://%s?cli=%s&user=%s',
            $this->getEndpoint(),
            urlencode($this->cliPath),
            urlencode($this->user)
        );
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    /**
     * @see https://github.com/AsamK/signal-cli
     */
    protected function doSend(MessageInterface $message): void
    {
        if (!$message instanceof SmsMessage) {
            throw new LogicException(sprintf(
                'The "%s" transport only supports instances of "%s" (instance of "%s" given).',
                __CLASS__,
                SmsMessage::class,
                get_debug_type($message)
            ));
        }

        $process = new Process([
            $this->cliPath,
            '-u',
            $this->user,
            'send',
            '-m',
            $message->getSubject(),
            $message->getPhone(),
        ]);

        $process->run();

        if (0 !== $process->getExitCode()) {
            throw new RuntimeException(sprintf(
                'Unable to post the Signal message: %s (%s).',
                $process->getExitCodeText(),
                $process->getExitCode()
            ));
        }
    }
}
