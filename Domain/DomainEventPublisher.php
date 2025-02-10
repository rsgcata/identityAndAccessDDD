<?php

namespace Domain;

use Exception;

interface DomainEventPublisher
{

    public function reset(): void;
    public function publish(AbstractDomainEvent $event): void;

    /**
     * Runs all the events through subscribers
     * @throws Exception
     */
    public function runEventsThroughSubscribers(): array;

    /**
     * @return AbstractDomainEvent[]
     */
    public function getPublishedEvents(): array;
}
