<?php
namespace Domain\IdentityAndAccess\Identity\User\Events;

use Domain\AbstractDomainEvent;
use Domain\Contact\FullName;
use Domain\IdentityAndAccess\Identity\User\actionAuthenticity\EmailAddressVerificationCode;
use Domain\IdentityAndAccess\Identity\User\User;
use Domain\IdentityAndAccess\Identity\User\UserEmailAddress;
use Domain\IdentityAndAccess\Identity\User\UserId;

class UserRegistered extends AbstractDomainEvent
{
    protected UserId $userId;
    protected UserEmailAddress $emailAddress;
    protected FullName $fullName;
    protected EmailAddressVerificationCode $emailAddressVerificationCode;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->emailAddress = $user->getEmailAddress();
        $this->emailAddressVerificationCode = $user->getEmailAddressVerificationCode();
        $this->fullName = $user->getPerson()->getFullName();
        $this->userId = $user->getId();
    }

    public function getEmailAddress(): UserEmailAddress
    {
        return $this->emailAddress;
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function getEmailAddressVerificationCode(): EmailAddressVerificationCode
    {
        return $this->emailAddressVerificationCode;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
