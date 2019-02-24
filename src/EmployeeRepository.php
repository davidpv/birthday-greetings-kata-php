<?php

declare(strict_types=1);


namespace BirthdayGreetingsKata;

interface EmployeeRepository
{
    public function byBirthday(XDate $xDate): array;
}
