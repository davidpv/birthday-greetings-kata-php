<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class AcceptanceTest extends TestCase
{
    private const EMPLOYEE_DATA_FILEPATH = __DIR__ . '/resources/employee_data.txt';
    private const SMTP_HOST = '127.0.0.1';
    private const SMTP_PORT = 1025;

    /**
     * @var BirthdayService
     */
    private $service;

    /** @before */
    protected function startMailhog(): void
    {
        $whichDockerCompose = Process::fromShellCommandline('which docker-compose');
        $whichDockerCompose->run();

        if ('' === $whichDockerCompose->getOutput()) {
            $this->markTestSkipped('To run this test you should have docker-compose installed.');
        }

        Process::fromShellCommandline('docker stop $(docker ps -a)')->run();
        Process::fromShellCommandline('docker-compose up -d')->run();
        
        $employeeRepository = new InMemoryEmployeeRepository();
        $employeeRepository->add(new Employee('John', 'Doe', '1982/10/08', 'john.doe@foobar.com'));
        $employeeRepository->add(new Employee('Mary', 'Ann', '1975/03/11', 'mary.ann@foobar.com'));

        $this->service = new BirthdayService($employeeRepository, new SwiftmailerBirthdayGreetSender(static::SMTP_HOST, static::SMTP_PORT));
    }

    /** @after */
    protected function stopMailhog(): void
    {
        (new Client())->delete('http://127.0.0.1:8025/api/v1/messages');
        Process::fromShellCommandline('docker-compose stop')->run();
        Process::fromShellCommandline('docker-compose rm -f')->run();
    }

    /**
     * @test
     */
    public function willSendGreetings_whenItsSomebodysBirthday(): void
    {
        $this->service->sendGreetings(
            new XDate('2008/10/08')
        );

        $messages = $this->messagesSent();
        $this->assertCount(1, $messages, 'message not sent?');

        $message = $messages[0];
        $this->assertEquals('Happy Birthday, dear John!', $message['Content']['Body']);
        $this->assertEquals('Happy Birthday!', $message['Content']['Headers']['Subject'][0]);
        $this->assertCount(1, $message['Content']['Headers']['To']);
        $this->assertEquals('john.doe@foobar.com', $message['Content']['Headers']['To'][0]);
    }

    /**
     * @test
     */
    public function willNotSendEmailsWhenNobodysBirthday(): void
    {
        $this->service->sendGreetings(
            new XDate('2008/01/01')
        );

        $this->assertCount(0, $this->messagesSent(), 'what? messages?');
    }

    private function messagesSent(): array
    {
        return json_decode(file_get_contents('http://127.0.0.1:8025/api/v1/messages'), true);
    }
}
