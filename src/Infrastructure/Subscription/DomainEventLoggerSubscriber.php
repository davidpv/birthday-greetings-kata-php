<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Infrastructure\Subscription;

use Ddd\Domain\DomainEvent;
use Ddd\Domain\DomainEventSubscriber;
use Ddd\Domain\Event\StoredEvent;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class DomainEventLoggerSubscriber implements DomainEventSubscriber
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(LoggerInterface $logger, NormalizerInterface $normalizer)
    {
        $this->logger = $logger;
        $this->normalizer = $normalizer;
    }

    /**
     * @param DomainEvent $aDomainEvent
     */
    public function handle($aDomainEvent): void
    {
        $context = $this->normalizer->normalize($aDomainEvent);
        
        $this->logger->info(
            get_class($aDomainEvent),
            $context
        );
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
