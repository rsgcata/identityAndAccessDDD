<?php

namespace Domain\IdentityAndAccess\Identity\User;

use Domain\Contact\FullName;
use Domain\Contact\PhoneNumber;
use DomainException;

class Person
{
    const int MAX_PHONE_NUMBERS = 3;

    private FullName $fullName;
    private UserAddress $address;

    /**
     * THe person's main phone number
     *
     * @var UserPhoneNumber[]
     * @access private
     */
    private array $userPhoneNumbers;

    private function __construct()
    {
    }

    /**
     * Create new person value object
     *
     * @throws DomainException
     */
    public static function create(FullName $fullName): self
    {
        $self = new self();
        $self->setFullName($fullName);
        $self->setAddress(UserAddress::create(null, null, null, null, null, null));
        $self->userPhoneNumbers = array();
        return $self;
    }

    /**
     * Change the full name
     *
     * @throws DomainException
     */
    public function changeFullName(FullName $fullName): void
    {
        $this->setFullName($fullName);
    }

    /**
     * Change the address
     *
     * @throws DomainException
     */
    public function changeAddress(UserAddress $address): void
    {
        $this->setAddress($address);
    }

    /**
     * @throws DomainException
     */
    public function addNewUserPhoneNumber(PhoneNumber $phoneNumber): void
    {
        $this->addPhoneNumber($phoneNumber);
    }

    /**
     * @throws DomainException
     */
    public function removePhoneNumber(PhoneNumber $phoneNumber): void
    {
        if ($this->getPrimaryUserPhoneNumber()->getPhoneNumber()->equals($phoneNumber)
            && count($this->userPhoneNumbers) > 1) {
            throw new DomainException(
                'Could not remove user phone number. The phone is set as primary.'
                . ' Another primary phone number should be selected first.'
            );
        }

        $phoneRemoved = false;

        foreach ($this->userPhoneNumbers as $k => $userPhone) {
            if ($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                unset($this->userPhoneNumbers[$k]);
                $phoneRemoved = true;
                break;
            }
        }

        if (!$phoneRemoved) {
            throw new DomainException(
                'Could not remove user phone number. Nonexistent phone number.'
            );
        }
    }

    /**
     * @throws DomainException
     */
    public function markPhoneNumberOwnershipAsVerified(PhoneNumber $phoneNumber): void
    {
        $phoneMarked = false;

        foreach ($this->userPhoneNumbers as $k => $userPhone) {
            if ($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                    $userPhone->getPhoneNumber(),
                    true,
                    $userPhone->getUsedAsPrimaryPhoneNumber());
                $phoneMarked = true;
                break;
            }
        }

        if (!$phoneMarked) {
            throw new DomainException(
                'Could not verify user phone number. Nonexistent phone number.'
            );
        }
    }

    /**
     * @throws DomainException
     */
    public function markPhoneNumberAsPrimary(PhoneNumber $phoneNumber): void
    {
        $phoneMarked = false;
        $phonesBackup = $this->userPhoneNumbers;

        foreach ($this->userPhoneNumbers as $k => $userPhone) {
            if ($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                if (!$userPhone->hasOwnershipVerified()) {
                    throw new DomainException(
                        'Could not mark phone number as primary.'
                        . ' Phone number not verified.');
                }

                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                    $userPhone->getPhoneNumber(),
                    $userPhone->getOwnershipVerified(),
                    true
                );
                $phoneMarked = true;
            } else {
                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                    $userPhone->getPhoneNumber(),
                    $userPhone->getOwnershipVerified(),
                    false
                );
            }
        }

        if (!$phoneMarked) {
            $this->userPhoneNumbers = $phonesBackup;
            throw new DomainException(
                'Could not mark phone number as primary. Nonexistent phone number.'
            );
        }
    }

    public function hasPhoneNumber(PhoneNumber $phoneNumber): bool
    {
        foreach ($this->userPhoneNumbers as $uph) {
            if ($uph->getPhoneNumber()->equals($phoneNumber)) {
                return true;
            }
        }

        return false;
    }

    public function hasPhoneNumberVerified(PhoneNumber $phoneNumber): bool
    {
        foreach ($this->userPhoneNumbers as $uph) {
            if ($uph->getPhoneNumber()->equals($phoneNumber)) {
                return $uph->hasOwnershipVerified();
            }
        }

        return false;
    }

    public function equals(Person $person): bool
    {
        $equalObjects = false;

        $phoneMatchedKeys = array();
        $userPhoneNumbersMatch = true;

        if (count($this->userPhoneNumbers) === count($person->getUserPhoneNumbers())) {
            foreach ($this->userPhoneNumbers as $userPhone) {
                $matchFound = false;
                foreach ($person->getUserPhoneNumbers() as $k => $uph) {
                    if (in_array($k, $phoneMatchedKeys)) {
                        continue;
                    }

                    if ($userPhone->equals($uph)) {
                        $phoneMatchedKeys[] = $k;
                        $matchFound = true;
                    }
                }

                if (!$matchFound) {
                    $userPhoneNumbersMatch = false;
                    break;
                }
            }
        }

        if (self::class === get_class($person)
            && $userPhoneNumbersMatch
            && $this->address->equals($person->getAddress())
            && $this->fullName->equals($person->getFullName())) {
            $equalObjects = true;
        }

        return $equalObjects;
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function getAddress(): UserAddress
    {
        return $this->address;
    }

    public function getUserPhoneNumbers(): array
    {
        return $this->userPhoneNumbers;
    }

    public function getPrimaryUserPhoneNumber(): ?UserPhoneNumber
    {
        foreach ($this->userPhoneNumbers as $number) {
            if ($number->isUsedAsPrimaryPhoneNumber()) {
                return $number;
            }
        }
        return null;
    }

    private function setFullName(FullName $fullName): void
    {
        if ($fullName->getFirstName() === null || $fullName->getLastName() === null) {
            throw new DomainException(
                'The full name of the person must contain at least a first name '
                . 'and a last name. Middle name is optional.');
        }

        $this->fullName = $fullName;
    }

    private function setAddress(UserAddress $address): void
    {
        $this->address = $address;
    }

    private function addPhoneNumber(PhoneNumber $phoneNumber): void
    {
        foreach ($this->userPhoneNumbers as $userPhone) {
            if ($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                throw new DomainException(
                    'Could not add phone number for person. The phone number'
                    . ' already exists.');
            }
        }

        $primary = empty($this->userPhoneNumbers);

        $this->userPhoneNumbers[] = UserPhoneNumber::create(
            $phoneNumber,
            false,
            $primary);
    }

    public static function reconstitute(
        FullName    $fullName,
        UserAddress $address,
        array       $userPhoneNumbers
    ): Person
    {
        $self = new self();
        $self->fullName = $fullName;
        $self->address = $address;
        $self->userPhoneNumbers = $userPhoneNumbers;
        return $self;
    }
}
