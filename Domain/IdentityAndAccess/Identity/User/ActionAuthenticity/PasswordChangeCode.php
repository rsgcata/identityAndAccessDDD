<?php

namespace Domain\IdentityAndAccess\Identity\User\ActionAuthenticity;

use DateInterval;
use DateTime;
use DomainException;
use Random\RandomException;

class PasswordChangeCode extends AbstractActionAuthenticityCode
{
    /**
     * @throws RandomException
     */
    public static function autoGenerateValidCode(): PasswordChangeCode
    {
        $self = new self();
        $self->code = $self->generateCode();
        $self->hashedCode = $self->protectCode($self->code);
        $self->setExpirationDateTime((new DateTime())->add(new DateInterval('PT10M')));
        return $self;
    }

    /**
     * @throws RandomException
     */
    protected function generateCode(): string
    {
        $initialStrongString = base64_encode(random_bytes(32));
        return mb_strtolower(
            mb_substr(preg_replace("/[^a-zA-Z0-9]+/u", "", $initialStrongString), 0, 9)
        );
    }

    /**
     * Check if this code equals a given code
     */
    public function codeEquals($code): bool
    {
        return password_verify(str_replace(' ', '', mb_strtolower($code)), $this->hashedCode);
    }

    protected function setCode($code): void
    {
        if (is_string($code)) {
            $code = str_replace(' ', '', mb_strtolower($code));

            if (preg_match('/^[a-z0-9]{9}$/i', $code) !== 1) {
                throw new DomainException('Could not set password change code. Invalid code.');
            }
        } else {
            throw new DomainException('Could not set password change code. Invalid code.');
        }

        $this->code = $code;
    }
}

