<?php

namespace Domain\IdentityAndAccess\Identity\User;

use Domain\Contact\PhoneNumber;
use DomainException;

class UserPhoneNumber
{
    private PhoneNumber $phoneNumber;
    private bool $ownershipVerified;
    private bool $usedAsPrimaryPhoneNumber;

    private function __construct()
    {
    }

    public static function create(
        PhoneNumber $phoneNumber,
        bool        $ownershipVerified,
        bool        $usedAsPrimaryPhoneNumber
    ): UserPhoneNumber
    {
        $self = new self();
        $self->setOwnershipVerified($ownershipVerified);
        $self->setPhoneNumber($phoneNumber);
        $self->setUsedAsPrimaryPhoneNumber($usedAsPrimaryPhoneNumber);
        return $self;
    }

    public function equals(UserPhoneNumber $userPhoneNumber): bool
    {
        $equalObjects = false;

        if (static::class === get_class($userPhoneNumber)
            && $this->phoneNumber->equals($userPhoneNumber->getPhoneNumber())
            && $this->ownershipVerified === $userPhoneNumber->getOwnershipVerified()
            && $this->usedAsPrimaryPhoneNumber === $userPhoneNumber->getUsedAsPrimaryPhoneNumber()) {
            $equalObjects = true;
        }

        return $equalObjects;
    }

    public function hasOwnershipVerified(): bool
    {
        return $this->ownershipVerified;
    }

    public function isUsedAsPrimaryPhoneNumber(): bool
    {
        return $this->usedAsPrimaryPhoneNumber;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getOwnershipVerified(): bool
    {
        return $this->ownershipVerified;
    }

    public function getUsedAsPrimaryPhoneNumber(): bool
    {
        return $this->usedAsPrimaryPhoneNumber;
    }

    public function setPhoneNumber(PhoneNumber $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function setOwnershipVerified($ownershipVerified): void
    {
        if (!is_bool($ownershipVerified)) {
            throw new DomainException(
                'Could not set ownership verified to user phone number. Invalid format.'
            );
        }

        $this->ownershipVerified = $ownershipVerified;
    }

    public function setUsedAsPrimaryPhoneNumber($usedAsPrimaryPhoneNumber): void
    {
        if (!is_bool($usedAsPrimaryPhoneNumber)) {
            throw new DomainException(
                'Could not set used as primary phone number to user phone number.'
                . ' Invalid format.'
            );
        }

        $this->usedAsPrimaryPhoneNumber = $usedAsPrimaryPhoneNumber;
    }

    public static function reconstitute(
        PhoneNumber $phoneNumber,
        bool        $ownershipVerified,
        bool        $usedAsPrimaryPhoneNumber
    ): self
    {
        $self = new self();
        $self->phoneNumber = $phoneNumber;
        $self->ownershipVerified = $ownershipVerified;
        $self->usedAsPrimaryPhoneNumber = $usedAsPrimaryPhoneNumber;
        return $self;
    }

}
