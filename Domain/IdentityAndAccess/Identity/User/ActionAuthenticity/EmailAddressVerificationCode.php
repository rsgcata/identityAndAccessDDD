<?php

namespace Domain\IdentityAndAccess\Identity\User\ActionAuthenticity;

use DomainException;
use Exception;

class EmailAddressVerificationCode
{
    private string $verificationCode;
    private string $hashedVerificationCode;

    private function __construct()
    {
    }

    /**
     * Create new email address verification code value object
     *
     * @throws DomainException
     */
    public static function create($code): self
    {
        $self = new self();
        $self->setVerificationCode($code);
        $self->hashedVerificationCode = $self->protectCode($self->verificationCode);
        return $self;
    }

    /**
     * @throws Exception
     */
    public static function autoGenerateValidCode(): self
    {
        $self = new self();
        $self->verificationCode = $self->generateVerificationCode();
        $self->hashedVerificationCode = $self->protectCode($self->verificationCode);
        return $self;
    }

    /**
     * Generates 256 characters long string code
     *
     * @throws Exception If the random bytes function could not generate the random bytes
     */
    private function generateVerificationCode(): string
    {
        return mb_substr(
            preg_replace(
                "/[^a-zA-Z0-9]+/u",
                "",
                base64_encode(random_bytes(300))
            ),
            0,
            256
        );
    }

    private function protectCode(string $code): string
    {
        return password_hash($code, PASSWORD_DEFAULT);
    }

    public function codeEquals(string $code): bool
    {
        return password_verify($code, $this->hashedVerificationCode);
    }

    /**
     * Check if this object equals another object
     */
    public function equals(EmailAddressVerificationCode $emailAddressVerificationCode): bool
    {
        return self::class === get_class($emailAddressVerificationCode) &&
            $this->hashedVerificationCode ===
            $emailAddressVerificationCode->getHashedVerificationCode();
    }

    public function getVerificationCode(): string
    {
        return $this->verificationCode;
    }

    public function getHashedVerificationCode(): string
    {
        return $this->hashedVerificationCode;
    }

    private function setVerificationCode($verificationCode): void
    {
        if (
            !is_string($verificationCode) ||
            preg_match('/^[a-z0-9]{256}$/i', $verificationCode) !== 1
        ) {
            throw new DomainException(
                'Could not set email address verification code. Invalid code.'
            );
        }

        $this->verificationCode = $verificationCode;
    }

    public static function reconstitute($hashedVerificationCode): self
    {
        $self = new self();
        $self->hashedVerificationCode = $hashedVerificationCode;
        return $self;
    }
}
