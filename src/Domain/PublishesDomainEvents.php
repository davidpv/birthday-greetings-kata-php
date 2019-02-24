<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Domain;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventPublisher;

trait PublishesDomainEvents
{
    private function publishThat(DomainEvent $event): void
    {
        DomainEventPublisher::instance()->publish($event);
    }
}
