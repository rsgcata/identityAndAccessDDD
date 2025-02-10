<?php

namespace Domain\WebLabel;

use DomainException;

class IpAddress
{
    /**
     * The internet protocol address
     */
    protected ?string $ipAddress;

    protected function __construct()
    {
        // Left blank specifically to allow factory methods to take full control over object state
        // consistency
    }

    /**
     * Create a new ip address value object
     * @throws DomainException
     */
    public static function create($ipAddress): static
    {
        $self = new static();
        $self->setIpAddress($ipAddress);
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(IpAddress $ipAddress): bool
    {
        return static::class === get_class($ipAddress) &&
            $this->ipAddress === $ipAddress->getIpAddress();
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    protected function setIpAddress($ipAddress): void
    {
        if ($ipAddress !== null) {
            $ipAddress = trim($ipAddress);

            if ($ipAddress === '') {
                $ipAddress = null;
            } else {
                if (!filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    throw new DomainException('Invalid IP address');
                }
            }
        }

        $this->ipAddress = $ipAddress;
    }

    public static function reconstitute($ipAddress): static
    {
        $self = new static();
        $self->ipAddress = $ipAddress;
        return $self;
    }
}
