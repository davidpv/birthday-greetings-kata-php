<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Infrastructure\Subscription;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventSubscriber;

final class DomainEventsSubscriber implements DomainEventSubscriber
{
    /**
     * @var DomainEvent[]
     */
    private $domainEvents;
    
    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle($aDomainEvent): void
    {
        $this->domainEvents[] = $aDomainEvent;
    }

    public function domainEvents(): array
    {
        return $this->domainEvents;
    }

    /**
     * @param DomainEvent $aDomainEvent
     * @return bool
     */
    public function isSubscribedTo($aDomainEvent): bool
    {
        return true;
    }
}
