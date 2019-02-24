<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Command;

use BirthdayGreetingsKata\Domain\BirthdayService;
use BirthdayGreetingsKata\Domain\XDate;

final class SendEmployeeBirthdayGreetsCommandHandler
{
    /**
     * @var BirthdayService
     */
    private $birthdayService;

    public function __construct(BirthdayService $birthdayService)
    {
        $this->birthdayService = $birthdayService;
    }

    public function handle(string $date): void
    {
        $this->birthdayService->sendGreetings(new XDate($date));
    }
}
