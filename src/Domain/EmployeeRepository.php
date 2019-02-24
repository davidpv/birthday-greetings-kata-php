<?php

declare(strict_types=1);


namespace BirthdayGreetingsKata\Domain;

interface EmployeeRepository
{
    public function byBirthday(XDate $xDate): array;
}
