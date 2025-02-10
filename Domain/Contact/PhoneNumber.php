<?php

namespace Domain\Contact;

use DomainException;

class PhoneNumber
{
    protected ?string $phoneNumber;

    protected function __construct()
    {
    }

    /**
     * Create new phone number value object
     *
     * @throws DomainException
     */
    public static function create($phoneNumber): static
    {
        $self = new static();
        $self->setPhoneNumber($phoneNumber);
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(PhoneNumber $phoneNumber): bool
    {
        return static::class === get_class($phoneNumber) &&
            $this->phoneNumber === $phoneNumber->getPhoneNumber();
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    protected function setPhoneNumber($phoneNumber): void
    {
        if ($phoneNumber !== null) {
            $phoneNumber = trim(str_replace(' ', '', $phoneNumber));

            if ($phoneNumber === '') {
                $phoneNumber = null;
            } else if (preg_match('/^\+?[0-9]{3,20}$/u', $phoneNumber) !== 1) {
                throw new DomainException('Invalid  phone number. Could not set the phone number.');
            }
        }

        $this->phoneNumber = $phoneNumber;
    }

    public static function reconstitute($phoneNumber): static
    {
        $self = new static();
        $self->phoneNumber = $phoneNumber;
        return $self;
    }
}
