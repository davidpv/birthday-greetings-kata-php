<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

final class BirthdayGreet
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $to;

    public function __construct(Employee $employee)
    {
        $this->from = 'sender@here.com';
        $this->title = 'Happy Birthday!';
        $this->message = sprintf('Happy Birthday, dear %s!', $employee->getFirstName());
        $this->to = $employee->getEmail();
    }

    public function from(): string
    {
        return $this->from;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function to(): string
    {
        return $this->to;
    }
}
