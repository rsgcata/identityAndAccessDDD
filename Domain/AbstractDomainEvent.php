<?php

namespace Domain;

use DateTime;
use DomainException;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractDomainEvent implements JsonSerializable
{
    /**
     * The event identifier
     */
    protected int $eventId;

    /**
     * The timestamp when the event occurred. Should also include microseconds for accuracy.
     * Microseconds also helps with deduplication
     */
    protected DateTime $occurredOn;

    /**
     * Object initializer
     */
    protected function __construct()
    {
        $this->occurredOn = new DateTime();
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function getOccurredOn(): DateTime
    {
        return $this->occurredOn;
    }

    /**
     * @throws DomainException If the domain event class is an anonymous type of class
     */
    public function getEventType(string $prefix = null): string
    {
        if ((new ReflectionClass($this))->isAnonymous()) {
            throw new DomainException(
                'Anonymous classes should not use the default getEventType' .
                ' method. They should have their own getEventType implementation.'
            );
        }

        return $prefix . '.' . str_replace('\\', '.', get_class($this));
    }

    /**
     * Helps into serializing the object in json format. Datetime is returned as timestamp in
     * microseconds format (timestamp.microseconds)
     */
    public function jsonSerialize(): array
    {
        return $this->getArrayFromObject($this);
    }

    final protected function getArrayFromObject($object): array
    {
        $resArray = array();

        $reflectionClass = new ReflectionClass($object);
        $properties = $reflectionClass->getProperties(
            ReflectionProperty::IS_PUBLIC |
            ReflectionProperty::IS_PROTECTED |
            ReflectionProperty::IS_PRIVATE
        );

        foreach ($properties as $prop) {
            $value = $prop->getValue($object);

            if (is_object($value)) {
                if ($value instanceof DateTime) {
                    $value = $value->format('U.u');
                } else {
                    $value = $this->getArrayFromObject($value);
                }
            }

            $resArray[$prop->getName()] = $value;
        }

        return $resArray;
    }
}
