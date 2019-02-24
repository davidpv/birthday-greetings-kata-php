<?php

declare(strict_types=1);


namespace BirthdayGreetingsKata\Domain;

interface BirthdayGreetSender
{
    public function send(BirthdayGreet $birthdayGreet): void;
}
