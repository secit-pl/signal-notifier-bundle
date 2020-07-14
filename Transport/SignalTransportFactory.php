<?php

declare(strict_types=1);

namespace SecIT\SignalNotifierBundle\Transport;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;

class SignalTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        $cliPath = $dsn->getOption('cli');
        $user = $dsn->getOption('user');

        if ('signal' === $scheme) {
            return (new SignalTransport($cliPath, $user, $this->dispatcher));
        }

        throw new UnsupportedSchemeException($dsn, 'signal', $this->getSupportedSchemes());
    }

    protected function getSupportedSchemes(): array
    {
        return ['signal'];
    }
}