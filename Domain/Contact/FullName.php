<?php

namespace Domain\Contact;

use DomainException;

class FullName
{
    public const int MIN_NAME_LENGTH = 2;
    public const int MAX_NAME_LENGTH = 128;

    protected ?string $firstName;
    protected ?string $middleName;
    protected ?string $lastName;

    protected function __construct()
    {
        // Left blank specifically to allow factory methods to take full control over object state
        // consistency
    }

    /**
     * Create new full name value object
     *
     * @throws DomainException
     */
    public static function create(
        ?string $firstName,
        ?string $middleName,
        ?string $lastName
    ): static
    {
        $self = new static();
        $self->setFirstName($firstName);
        $self->setLastName($lastName);
        $self->setMiddleName($middleName);
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(FullName $fullName): bool
    {
        return static::class === get_class($fullName) &&
            $this->firstName === $fullName->getFirstName() &&
            $this->middleName === $fullName->getMiddleName() &&
            $this->lastName === $fullName->getLastName();
    }

    /**
     * Check if all descriptors are null
     */
    public function hasAllDescriptorsNull(): bool
    {
        return $this->firstName === null &&
            $this->middleName === null &&
            $this->lastName === null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    protected function assertIntlPersonName(string $value, DomainException $exception): void
    {
        if (
            mb_strlen($value) < static::MIN_NAME_LENGTH ||
            mb_strlen($value) > static::MAX_NAME_LENGTH ||
            preg_match(
                '/^([\p{L}\p{Mn}\p{Pd}]+(\s|-|\'|\x{2019}|\.)?[\p{L}\p{Mn}\p{Pd}]+)+$/u',
                $value
            ) !== 1
        ) {
            throw $exception;
        }
    }

    protected function setFirstName(?string $firstName): void
    {
        if ($firstName !== null) {
            $firstName = ucfirst(trim($firstName));

            if ($firstName === '') {
                $firstName = null;
            } else {
                $this->assertIntlPersonName(
                    $firstName,
                    new DomainException(
                        'Invalid first name. Could not set first name.'
                    )
                );
            }
        }

        $this->firstName = $firstName;
    }

    protected function setMiddleName(?string $middleName): void
    {
        if ($middleName !== null) {
            $middleName = ucfirst(trim($middleName));

            if ($middleName === '') {
                $middleName = null;
            } else {
                $this->assertIntlPersonName(
                    $middleName,
                    new DomainException(
                        'Invalid middle name. Could not set middle name.'
                    )
                );
            }
        }

        $this->middleName = $middleName;
    }

    protected function setLastName(?string $lastName): void
    {
        if ($lastName !== null) {
            $lastName = ucfirst(trim($lastName));

            if ($lastName === '') {
                $lastName = null;
            } else {
                $this->assertIntlPersonName(
                    $lastName,
                    new DomainException(
                        'Invalid last name. Could not set last name.'
                    )
                );
            }
        }

        $this->lastName = $lastName;
    }

    public static function reconstitute(
        ?string $firstName,
        ?string $middleName,
        ?string $lastName
    ): static
    {
        $self = new static();
        $self->firstName = $firstName;
        $self->middleName = $middleName;
        $self->lastName = $lastName;
        return $self;
    }
}
