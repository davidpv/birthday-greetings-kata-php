<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

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
