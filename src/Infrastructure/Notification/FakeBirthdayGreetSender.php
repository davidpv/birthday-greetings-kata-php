<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Infrastructure\Notification;

use BirthdayGreetingsKata\Domain\BirthdayGreet;
use BirthdayGreetingsKata\Domain\BirthdayGreetSender;

final class FakeBirthdayGreetSender implements BirthdayGreetSender
{
    /**
     * @var BirthdayGreet[] 
     */
    private $sentGreets = [];

    public function send(BirthdayGreet $birthdayGreet): void
    {
        $this->sentGreets[] = $birthdayGreet;
    }

    public function sentGreets(): array
    {
        return $this->sentGreets;
    }
}
