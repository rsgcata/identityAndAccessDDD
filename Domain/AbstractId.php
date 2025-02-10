<?php

namespace Domain;

use DomainException;

abstract class AbstractId
{
    protected int|string|null $id;

    final protected function __construct()
    {

    }

    /**
     * Create new id value object
     *
     * @throws DomainException
     */
    public static function create(int|string|null $id): static
    {
        $self = new static();
        $self->setId($id);
        return $self;
    }

    /**
     * Create a new, null id value object
     */
    public static function createNull(): static
    {
        $self = new static();
        $self->id = null;
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(AbstractId $id): bool
    {
        return static::class === get_class($id) && $this->id === $id->getId();
    }

    /**
     * Reconstitutes the value object. Should be used only by repositories when reconstituting
     * an already persisted value object
     */
    public static function reconstitute($id): static
    {
        $self = new static();
        $self->id = $id;
        return $self;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    protected abstract function setId(int|string|null $id);
}
