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

    /**
     * @var string
     */
    private $employeeEmail;

    public function __construct(string $employeeEmail)
    {
        $this->occurredOn = new \DateTime();
        $this->employeeEmail = $employeeEmail;
    }

    public function employeeEmail(): string
    {
        return $this->employeeEmail;
    }

    /**
     * @return \DateTime
     */
    public function occurredOn(): \DateTimeInterface
    {
        return $this->occurredOn;
    }

    public static function now(Employee $toEmployee): self
    {
        return new static($toEmployee->getEmail());
    }
}
