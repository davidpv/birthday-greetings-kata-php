<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Command;

final class SendEmployeeBirthdayGreetsCommand
{
    /**
     * @var string
     */
    private $date;

    public function __construct(string $date)
    {
        $this->date = $date;
    }

    public function date(): string
    {
        return $this->date;
    }
}
