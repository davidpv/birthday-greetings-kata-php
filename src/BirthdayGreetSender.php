<?php

declare(strict_types=1);


namespace BirthdayGreetingsKata;

interface BirthdayGreetSender
{
    public function send(BirthdayGreet $birthdayGreet): void;
}
