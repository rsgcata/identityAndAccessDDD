<?php

namespace Domain\Contact;

use DomainException;

class Address
{
    public const int COUNTRY_CODE_LENGTH = 2;
    public const int MAX_STATE_LENGTH = 100;
    public const int MIN_STATE_LENGTH = 2;

    /**
     * The countryCode (as in US)
     */
    protected ?string $countryCode;

    /**
     * The state (as in California)
     */
    protected ?string $state;

    /**
     * The county / region
     */
    protected ?string $county;

    /**
     * The city (as in Los Angeles)
     */
    protected ?string $city;

    /**
     * The street address (as in Grand Boulevard)
     */
    protected ?string $streetAddress;

    /**
     * The zip code
     */
    protected ?string $zipCode;

    /**
     * The area code
     */
    protected ?string $areaCode;

    protected function __construct()
    {
        // Left blank specifically to allow factory methods to take full control over object state
        // consistency
    }

    /**
     * Creates new Address value object
     */
    public static function create(
        ?string $countryCode,
        ?string $state,
        ?string $county,
        ?string $city,
        ?string $streetAddress,
        ?string $zipCode,
        ?string $areaCode
    ): static
    {
        $self = new static();
        $self->setCity($city);
        $self->setCountryCode($countryCode);
        $self->setCounty($county);
        $self->setState($state);
        $self->setStreetAddress($streetAddress);
        $self->setZipCode($zipCode);
        $self->setAreaCode($areaCode);
        return $self;
    }

    /**
     * Check if this object equals another object
     */
    public function equals(Address $address): bool
    {
        return static::class === get_class($address) &&
            $this->city === $address->getCity() &&
            $this->countryCode === $address->getCountryCode() &&
            $this->county === $address->getCounty() &&
            $this->state === $address->getState() &&
            $this->streetAddress === $address->getStreetAddress() &&
            $this->zipCode === $address->getZipCode() &&
            $this->areaCode === $address->getAreaCode();
    }

    /**
     * Check if it has all fields/descriptors null
     */
    public function hasAllDescriptorsNull(): bool
    {
        if (
            $this->city !== null ||
            $this->countryCode !== null ||
            $this->county !== null ||
            $this->state !== null ||
            $this->streetAddress !== null ||
            $this->zipCode !== null ||
            $this->areaCode !== null
        ) {
            return false;
        }
        return true;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function getAreaCode(): ?string
    {
        return $this->areaCode;
    }

    protected function setCountryCode(?string $countryCode): void
    {
        if ($countryCode !== null) {
            $countryCode = strtoupper(trim($countryCode));

            if ($countryCode === '') {
                $countryCode = null;
            } else if (strlen($countryCode) !== static::COUNTRY_CODE_LENGTH) {
                throw new DomainException(
                    sprintf(
                        'Invalid generic country code. Country code should be %s characters long.',
                        static::COUNTRY_CODE_LENGTH
                    )
                );
            }
        }

        $this->countryCode = $countryCode;
    }

    /**
     * @throws DomainException
     */
    protected function assertValidRegionName(string $state, string $errRegion = 'state'): void
    {
        if (
            strlen($state) < static::MIN_STATE_LENGTH ||
            strlen($state) > static::MAX_STATE_LENGTH
        ) {
            throw new DomainException(
                sprintf(
                    'Invalid %s name. Characters count must be between %s and %s.',
                    $errRegion,
                    static::MIN_STATE_LENGTH,
                    static::MAX_STATE_LENGTH
                ),
            );
        }

        if (
            preg_match(
                '/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*\p{L}+[\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*\s?)+$/u',
                $state
            ) !== 1
        ) {
            throw new DomainException(
                sprintf(
                    'Invalid %s name. Only international alphabet letters, single quotes, ' .
                    'numbers, spaces, hyphens and dots are allowed.',
                    $errRegion,
                )
            );
        }
    }

    protected function setState(?string $state): void
    {
        if ($state !== null) {
            $state = trim($state);

            if ($state === '') {
                $state = null;
            } else {
                $this->assertValidRegionName($state);
            }
        }

        $this->state = $state;
    }

    protected function setCounty(?string $county): void
    {
        if ($county !== null) {
            $county = trim($county);

            if ($county === '') {
                $county = null;
            } else {
                $this->assertValidRegionName($county, 'country');
            }
        }

        $this->county = $county;
    }

    protected function setCity($city): void
    {
        if ($city !== null) {
            if (is_string($city)) {
                $city = trim($city);
            }

            if ($city === '') {
                $city = null;
            } else {
                $this->assertValidRegionName($city, 'city');
            }
        }

        $this->city = $city;
    }

    protected function setStreetAddress(?string $streetAddress): void
    {
        if ($streetAddress !== null) {
            $streetAddress = trim($streetAddress);

            if ($streetAddress === '') {
                $streetAddress = null;
            } else {
                if (
                    strlen($streetAddress) < static::MIN_STATE_LENGTH ||
                    strlen($streetAddress) > static::MAX_STATE_LENGTH
                ) {
                    throw new DomainException(
                        'Invalid street address. Street address must be between '
                        . static::MIN_STATE_LENGTH . ' and ' . static::MAX_STATE_LENGTH
                    );
                }

                if (
                    preg_match(
                        '/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9,.\-;]+\s?)+$/u',
                        $streetAddress
                    ) !== 1
                ) {
                    throw new DomainException(
                        'Invalid street address. Only international alphabet letters, single' .
                        ' quotes, commas, periods, hyphens, numbers, semicolons and spaces are' .
                        ' allowed.'
                    );
                }
            }
        }

        $this->streetAddress = $streetAddress;
    }

    protected function setZipCode(?string $zipCode): void
    {
        if ($zipCode !== null) {
            $zipCode = trim(str_replace(' ', '', $zipCode));

            if ($zipCode === '') {
                $zipCode = null;
            } else {
                if (preg_match('/^([\p{L}\p{Mn}\p{Pd}0-9]+\s?)+$/u', $zipCode) !== 1) {
                    throw new DomainException(
                        'Invalid zip code. Only international alphabet letters, spaces '
                        . 'and numbers are allowed.'
                    );
                }
            }
        }

        $this->zipCode = $zipCode;
    }

    protected function setAreaCode(?string $areaCode): void
    {
        if ($areaCode !== null) {
            $areaCode = trim($areaCode);

            if ($areaCode === '') {
                $areaCode = null;
            } else {
                if (preg_match('/^([\p{L}\p{Mn}\p{Pd}0-9]+\s?)+$/u', $areaCode)!== 1) {
                    throw new DomainException(
                        'Invalid area code. Only international alphabet letters, spaces '
                        . 'and numbers area allowed.'
                    );
                }
            }
        }

        $this->areaCode = $areaCode;
    }

    public static function reconstitute(
        ?string $countryCode,
        ?string $state,
        ?string $county,
        ?string $city,
        ?string $streetAddress,
        ?string $zipCode,
        ?string $areaCode,
    ): static
    {
        $self = new static();
        $self->countryCode = $countryCode;
        $self->state = $state;
        $self->county = $county;
        $self->city = $city;
        $self->streetAddress = $streetAddress;
        $self->zipCode = $zipCode;
        $self->areaCode = $areaCode;
        return $self;
    }
}
