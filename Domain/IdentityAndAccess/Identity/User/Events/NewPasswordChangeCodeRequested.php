<?php

namespace Domain\IdentityAndAccess\Identity\User\Events;

use Domain\AbstractDomainEvent;
use Domain\Contact\FullName;
use Domain\IdentityAndAccess\Identity\User\ActionAuthenticity\PasswordChangeCode;
use Domain\IdentityAndAccess\Identity\User\User;
use Domain\IdentityAndAccess\Identity\User\UserEmailAddress;
use Domain\IdentityAndAccess\Identity\User\UserId;
use Domain\IdentityAndAccess\Identity\User\UserPhoneNumber;

class NewPasswordChangeCodeRequested extends AbstractDomainEvent
{
    private UserId $userId;
    private UserEmailAddress $emailAddress;
    private UserPhoneNumber $phoneNumber;
    private FullName $fullName;
    private PasswordChangeCode $passwordChangeCode;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->emailAddress = $user->getEmailAddress();
        $this->phoneNumber = $user->getPerson()->getPrimaryUserPhoneNumber();
        $this->passwordChangeCode = $user->getPasswordChangeCode();
        $this->fullName = $user->getPerson()->getFullName();
        $this->userId = $user->getId();
    }

    public function getEmailAddress(): UserEmailAddress
    {
        return $this->emailAddress;
    }

    public function getPhoneNumber(): UserPhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function getPasswordChangeCode(): PasswordChangeCode
    {
        return $this->passwordChangeCode;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
