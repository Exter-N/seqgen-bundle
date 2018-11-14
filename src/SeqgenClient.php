<?php

/*
 * This file is part of exter-n/seqgen-bundle.
 *
 * Copyright (C) Exter-N
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Exn\SeqgenBundle;

use Symfony\Component\Stopwatch\Stopwatch;

class SeqgenClient implements IdentifierGeneratorInterface
{
    private const IDS_PER_REQUEST = 64;

    /** @var resource */
    private $socket;

    /** @var Stopwatch|null */
    private $stopwatch;

    /** @var array */
    private $queue;

    public function __construct(string $hostAndPort, ?Stopwatch $stopwatch)
    {
        $this->socket = \fsockopen('udp://'.$hostAndPort);
        $this->stopwatch = $stopwatch;
        $this->queue = [];
    }

    public function __destruct()
    {
        \fclose($this->socket);
    }

    public function generateOne(): int
    {
        $this->ensure(1);

        return \array_shift($this->queue);
    }

    public function generate(int $howMany): array
    {
        $this->ensure($howMany);

        return \array_splice($this->queue, 0, $howMany);
    }

    private function ensure(int $howMany): void
    {
        $requests = \intdiv($howMany - \count($this->queue) + self::IDS_PER_REQUEST - 1, self::IDS_PER_REQUEST);
        if ($requests <= 0) {
            return;
        }

        while ($requests-- > 0) {
            \fwrite($this->socket, \chr(self::IDS_PER_REQUEST));
            $raw = \fread($this->socket, self::IDS_PER_REQUEST * 8);
            if (empty($raw)) {
                throw new \RuntimeException('No identifiers returned, is the seqgen server active?');
            }
            for ($num = self::IDS_PER_REQUEST, $pos = 0; $num-- > 0; $pos += 8) {
                $unpacked = \unpack('N2', \substr($raw, $pos, 8));
                $this->queue[] = ($unpacked[1] << 32) | $unpacked[2];
            }
        }
    }
}
