<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Domain;

use Ddd\Domain\DomainEvent;

final class BirthdayGreetWasSent implements DomainEvent
{
    /**
     * @var \DateTimeInterface
     */
    private $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new \DateTimeImmutable();
    }

    /**
     * @return \DateTime
     */
    public function occurredOn(): \DateTimeInterface
    {
        return $this->occurredOn;
    }

    public static function now(): self
    {
        return new static();
    }
}
