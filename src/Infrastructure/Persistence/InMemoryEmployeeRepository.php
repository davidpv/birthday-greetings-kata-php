<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Infrastructure\Persistence;

use BirthdayGreetingsKata\Domain\Employee;
use BirthdayGreetingsKata\Domain\EmployeeRepository;
use BirthdayGreetingsKata\Domain\XDate;

final class InMemoryEmployeeRepository implements EmployeeRepository
{
    /**
     * @var Employee[]
     */
    private $employees = [];

    public function add(Employee $employee): void 
    {
        $this->employees[] = $employee;
    }

    public function byBirthday(XDate $xDate): array
    {
        return array_filter(
            $this->employees,
            function(Employee $employee) use ($xDate): bool {
                return $employee->isBirthday($xDate);
            }
        );
    }
}
