<?php

declare(strict_types=1);

namespace BirthdayGreetingsKata\Infrastructure\Subscription;

use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DomainEventsPersister
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

    public function persist(array $domainEvents): void
    {
        foreach ($domainEvents as $aDomainEvent) {
            $context = $this->normalizer->normalize($aDomainEvent);

            $this->logger->info(
                get_class($aDomainEvent),
                $context
            );
        }
    }
}
