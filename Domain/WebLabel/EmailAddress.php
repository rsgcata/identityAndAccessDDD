<?php

namespace Domain\WebLabel;

use DomainException;

class EmailAddress
{
    protected ?string $emailAddress;

    protected function __construct()
    {
        // Left blank specifically to allow factory methods to take full control over object state
        // consistency
    }

    public static function create($emailAddress): static
    {
        $self = new static();
        $self->setEmailAddress($emailAddress);
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(EmailAddress $emailAddress): bool
    {
        return static::class === get_class($emailAddress) &&
            $this->emailAddress === $emailAddress->getEmailAddress();
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function getWebDomainNamePart(): ?string
    {
        if ($this->emailAddress === null) {
            return null;
        }

        return explode('@', $this->emailAddress)[1];
    }

    /**
     * Set the email address
     * @throws DomainException
     */
    protected function setEmailAddress(?string $emailAddress): void
    {
        if ($emailAddress !== null) {
            $emailAddress = trim($emailAddress);

            if ($emailAddress === '') {
                $emailAddress = null;
            } else if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                throw new DomainException('Invalid email address');
            }
        }

        $this->emailAddress = $emailAddress;
    }

    public static function reconstitute($emailAddress): static
    {
        $self = new static();
        $self->emailAddress = $emailAddress;
        return $self;
    }

}
