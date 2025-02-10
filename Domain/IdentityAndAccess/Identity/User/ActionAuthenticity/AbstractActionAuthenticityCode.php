<?php
namespace Domain\IdentityAndAccess\Identity\User\actionAuthenticity;

use DateTime;
use DomainException;

abstract class AbstractActionAuthenticityCode
{
    /**
     * The hashed code
     */
    protected string $hashedCode;

    /**
     * The code
     */
    protected string $code;

    /**
     * The date and time when the code will expire
     */
    protected DateTime $expirationDateTime;

    protected function __construct()
    {
    }

    /**
     * Create value object
     *
     * @throws DomainException
     */
    public static function create($code): static
    {
        $self = new static();
        $self->setCode($code);
        $self->hashedCode = $self->protectCode($self->code);
        $self->setExpirationDateTime(new DateTime());
        return $self;
    }

    /**
     * Auto generate expired code
     */
    public static function autoGenerateExpiredCode(): static
    {
        $self = new static();
        $self->code = $self->generateCode();
        $self->hashedCode = $self->protectCode($self->code);
        $self->setExpirationDateTime(new DateTime('2000-01-01'));
        return $self;
    }

    /**
     * Generates a string code
     */
    abstract protected function generateCode(): string;

    protected function protectCode(string $code): string
    {
        return password_hash($code, PASSWORD_DEFAULT);
    }

    /**
     * Check if this code equals a given code
     */
    abstract public function codeEquals(string $code): bool;

    /**
     * Check if this object equals another object
     */
    public function equals(AbstractActionAuthenticityCode $actionAuthenticityCode): bool
    {
        return static::class === get_class($actionAuthenticityCode) &&
            $this->hashedCode === $actionAuthenticityCode->getHashedCode() &&
            $this->expirationDateTime->getTimestamp() ===
            $actionAuthenticityCode->getExpirationDateTime()->getTimestamp();
    }

    /**
     * Checks if the code is expired
     */
    public function isExpired(): bool
    {
        return $this->expirationDateTime->getTimestamp() < (new DateTime())->getTimestamp();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getHashedCode(): string
    {
        return $this->hashedCode;
    }

    public function getExpirationDateTime(): DateTime
    {
        return $this->expirationDateTime;
    }

    abstract protected function setCode($code): void;

    protected function setExpirationDateTime(DateTime $expirationDateTime): void
    {
        $this->expirationDateTime = $expirationDateTime;
    }

    public static function reconstitute(string $hashedCode, DateTime $expirationDateTime): static
    {
        $self = new static();
        $self->hashedCode = $hashedCode;
        $self->expirationDateTime = $expirationDateTime;
        return $self;
    }
}

