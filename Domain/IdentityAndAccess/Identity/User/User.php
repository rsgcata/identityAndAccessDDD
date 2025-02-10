<?php

namespace Domain\IdentityAndAccess\Identity\User;

use DateTime;
use Domain\Contact\FullName;
use Domain\Contact\PhoneNumber;
use Domain\DomainEventPublisher;
use Domain\IdentityAndAccess\Identity\User\actionAuthenticity\EmailAddressVerificationCode;
use Domain\IdentityAndAccess\Identity\User\actionAuthenticity\PasswordChangeCode;
use Domain\IdentityAndAccess\Identity\User\events\NewPasswordChangeCodeRequested;
use Domain\IdentityAndAccess\Identity\User\events\ResendEmailVerificationCodeRequested;
use Domain\IdentityAndAccess\Identity\User\events\UserEmailAddressChanged;
use Domain\IdentityAndAccess\Identity\User\events\UserRegistered;
use DomainException;
use Exception;

class User
{
    public const int MIN_PASSWORD_LENGTH = 8;
    public const int MAX_PASSWORD_LENGTH = 64;

    private UserId $id;
    private UserEmailAddress $emailAddress;
    private EmailAddressVerificationCode $emailAddressVerificationCode;
    private string $password;
    private PasswordChangeCode $passwordChangeCode;
    private Person $person;
    private bool $emailAddressOwnershipVerified;
    private DateTime $registrationDate;

    private function __construct()
    {
    }

    /**
     * Register a new user
     *
     * @throws DomainException|Exception
     */
    public static function registerUser(
        UserEmailAddress     $emailAddress,
        Person               $person,
        string               $password,
        EncryptionService    $encryptionService,
        DomainEventPublisher $eventPublisher
    ): User
    {
        $self = new self();
        $self->id = UserId::createNull();
        $self->password = $self->protectPassword($password, $encryptionService);
        $self->setEmailAddress($emailAddress);
        $self->setEmailAddressOwnershipVerified(FALSE);
        $self->setEmailAddressVerificationCode(
            EmailAddressVerificationCode::autoGenerateValidCode());
        $self->setPerson($person);
        $self->setPasswordChangeCode(PasswordChangeCode::autoGenerateExpiredCode());
        $self->registrationDate = new \DateTime();
        $eventPublisher->publish(new UserRegistered($self));

        return $self;
    }

    /**
     * @throws Exception
     */
    public function resendEmailVerificationCode(DomainEventPublisher $eventPublisher): void
    {
        if ($this->hasVerifiedEmailAddress()) {
            throw new DomainException(
                'Could not resend email verification code. The email is already verified.'
            );
        }

        $this->setEmailAddressVerificationCode(
            EmailAddressVerificationCode::autoGenerateValidCode());
        $eventPublisher->publish(new ResendEmailVerificationCodeRequested($this));
    }

    /**
     * @throws DomainException
     */
    private function protectPassword(string $password, EncryptionService $encryptionService): string
    {
        if (
            strlen($password) < self::MIN_PASSWORD_LENGTH ||
            strlen($password) > self::MAX_PASSWORD_LENGTH
        ) {
            throw new DomainException(
                'The user password does not fulfill the requirements of a strong password.' .
                ' It requires minimum 8 characters.'
            );
        }

        if (preg_match('/\p{Ll}+/u', $password) !== 1
            || preg_match('/\p{Lu}+/u', $password) !== 1
            || preg_match('/\p{N}+/u', $password) !== 1
            || preg_match('/\p{P}+/u', $password) !== 1) {
            throw new DomainException(
                'The user password does not fulfill the requirements of a '
                . 'strong password! It requires at least one of each of the following charters : '
                . 'an uppercase letter, a lowercase letter, a number and a punctuation mark.'
            );
        }

        return $encryptionService->hashUserPassword($password);
    }

    /**
     * Verify and validate email address ownership
     *
     * @throws DomainException
     */
    public function verifyEmailAddressOwnership(EmailAddressVerificationCode $code): void
    {
        if (!$this->emailAddressVerificationCode->codeEquals($code->getVerificationCode())) {
            throw new DomainException('The email address could not be verified! Invalid code!');
        }

        $this->emailAddressOwnershipVerified = TRUE;
    }

    /**
     * Expose if the user has email address verified
     */
    public function hasVerifiedEmailAddress(): bool
    {
        return $this->emailAddressOwnershipVerified;
    }

    /**
     * Change the full name
     */
    public function changeFullName(FullName $fullName): void
    {
        $this->person->changeFullName($fullName);
    }

    /**
     * Change the address
     *
     * @throws DomainException
     */
    public function changeAddress(UserAddress $address): void
    {
        $this->person->changeAddress($address);
    }

    /**
     * Add a phone number
     *
     * @throws DomainException
     */
    public function addPhoneNumber(PhoneNumber $phoneNumber): void
    {
        if (!$this->emailAddressOwnershipVerified) {
            throw new DomainException(
                'Could not add phone number.'
                . ' The email address ownership has not been verified yet.'
            );
        }

        if (count($this->person->getUserPhoneNumbers()) === Person::MAX_PHONE_NUMBERS) {
            throw new DomainException(
                'Could not add phone number. The max phone numbers limit'
                . ' has been reached.'
            );
        }

        $this->person->addNewUserPhoneNumber($phoneNumber);
    }

    /**
     * Send the phone number verification code. Since the code should be sent before adding a phone
     * number, there should be checked if a new phone can be added (max count check). If code
     * should be sent after the phone number was added, the count check should be removed
     *
     * @throws DomainException
     */
    public function sendPhoneNumberVerificationCode(): void
    {
        if (!$this->emailAddressOwnershipVerified) {
            throw new DomainException(
                'Could not send phone number verification code.'
                . ' The email address ownership has not been verified yet.');
        }

        if (count($this->person->getUserPhoneNumbers()) === Person::MAX_PHONE_NUMBERS) {
            throw new DomainException(
                'Could not send phone number verification code.'
                . ' The max phone numbers limit has been reached.');
        }
    }

    /**
     * Remove a phone number
     * @throws DomainException
     */
    public function removePhoneNumber(PhoneNumber $phoneNumber): void
    {
        $this->person->removePhoneNumber($phoneNumber);
    }

    /**
     * @throws DomainException
     */
    public function markPhoneNumberOwnershipAsVerified(PhoneNumber $phoneNumber): void
    {
        $this->person->markPhoneNumberOwnershipAsVerified($phoneNumber);
    }

    /**
     * @throws DomainException
     */
    public function markPhoneNumberAsPrimary(PhoneNumber $phoneNumber): void
    {
        $this->person->markPhoneNumberAsPrimary($phoneNumber);
    }

    /**
     * Change the phone number
     *
     * @throws DomainException|Exception
     */
    public function changeEmailAddress(
        UserEmailAddress  $emailAddress,
        string            $plainPassword,
        EncryptionService $encryptionService
    )
    {
        if (!$encryptionService->plainUserPasswordMatchingHashedPassword(
            $plainPassword,
            $this->password)) {
            throw new DomainException('Could not change the email address. Password missmatch.');
        }

        $this->setEmailAddressOwnershipVerified(FALSE);
        $this->setEmailAddressverificationCode(
            EmailAddressVerificationCode::autoGenerateValidCode());
        $this->setEmailAddress($emailAddress);

        DomainEventPublisher::getInstance()->publish(new UserEmailAddressChanged($this));
    }

    /**
     * Set's / generates a new password change code as requested
     */
    public function requestNewPasswordChangeCode()
    {
        $this->setPasswordChangeCode(PasswordChangeCode::autoGenerateValidCode());
        DomainEventPublisher::getInstance()->publish(new NewPasswordChangeCodeRequested($this));
    }

    /**
     * Change the phone number
     *
     * @throws DomainException
     */
    public function changeForgottenPassword(
        $passwordChangeCode,
        $plainNewPassword,
        EncryptionService $encryptionService
    )
    {
        if ($this->passwordChangeCode->isExpired()) {
            throw new DomainException(
                'Could not change the forgotten password. Password change'
                . ' code is expired.');
        }

        if (!$this->passwordChangeCode->codeEquals($passwordChangeCode)) {
            throw new DomainException(
                'Could not change the forgotten password. Password change'
                . ' code missmatch.');
        }

        $this->password = $this->protectPassword($plainNewPassword, $encryptionService);
        $this->setPasswordChangeCode(PasswordChangeCode::autoGenerateExpiredCode());
    }

    /**
     * Change known password
     *
     * @throws DomainException
     */
    public function changeKnownPassword(
        $plainNewPassword,
        $plainOldPassword,
        EncryptionService $encryptionService
    )
    {
        if (!$encryptionService->plainUserPasswordMatchingHashedPassword(
            $plainOldPassword,
            $this->password)) {
            throw new DomainException('Could not change the known password. Password missmatch.');
        }

        $this->password = $this->protectPassword($plainNewPassword, $encryptionService);
    }

    /**
     * Deletes the user account
     *
     * @throws DomainException
     */
    public function deleteAccount(
        $password,
        EncryptionService $encryptionService
    )
    {
        // This condition is needed so bad intended people can't create accounts with random emails
        // and after delete them so the real email owner can't use it anymore
        if (!$this->emailAddressOwnershipVerified) {
            throw new DomainException(
                'Could not delete user account.'
                . ' The email address is not verified.');
        }

        if (!$encryptionService->plainUserPasswordMatchingHashedPassword(
            $password,
            $this->password)) {
            throw new DomainException('Could not delete user account. Password missmatch.');
        }
    }

    /**
     * @return UserId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserEmailAddress
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return EmailAddressVerificationCode
     */
    public function getEmailAddressverificationCode()
    {
        return $this->emailAddressVerificationCode;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return PasswordChangeCode
     */
    public function getPasswordChangeCode()
    {
        return $this->passwordChangeCode;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function getEmailAddressOwnershipVerified()
    {
        return $this->emailAddressOwnershipVerified;
    }

    /**
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    private function setEmailAddress(UserEmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    private function setEmailAddressVerificationCode(
        EmailAddressVerificationCode $emailAddressVerificationCode
    )
    {
        $this->emailAddressVerificationCode = $emailAddressVerificationCode;
    }

    private function setPerson(Person $person)
    {
        $this->person = $person;
    }

    private function setEmailAddressOwnershipVerified($emailAddressOwnershipVerified)
    {
        if (!is_bool($emailAddressOwnershipVerified)) {
            throw new DomainException(
                'Could not set email address ownership verified to user. Should'
                . ' be boolean.');
        }

        $this->emailAddressOwnershipVerified = $emailAddressOwnershipVerified;
    }

    private function setPasswordChangeCode(PasswordChangeCode $passwordChangeCode)
    {
        $this->passwordChangeCode = $passwordChangeCode;
    }

    /**
     * @return User
     */
    public static function reconstitute(
        UserId                       $userId,
        UserEmailAddress             $emailAddress,
        EmailAddressVerificationCode $emailAddressVerificationCode,
                                     $password,
        PasswordChangeCode           $passwordChangeCode,
        Person                       $person,
                                     $emailAddressOwnershipVerified,
        \DateTime                    $registrationDate
    )
    {
        $self = new self();
        $self->id = $userId;
        $self->emailAddress = $emailAddress;
        $self->emailAddressVerificationCode = $emailAddressVerificationCode;
        $self->password = $password;
        $self->passwordChangeCode = $passwordChangeCode;
        $self->person = $person;
        $self->emailAddressOwnershipVerified = $emailAddressOwnershipVerified;
        $self->registrationDate = $registrationDate;
        return $self;
    }
}
