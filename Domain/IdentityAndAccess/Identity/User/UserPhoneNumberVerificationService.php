<?php
namespace Domain\IdentityAndAccess\Identity\User;

use Domain\Contact\PhoneNumber;
use Domain\IdentityAndAccess\Identity\User\ActionAuthenticity\UserPhoneNumberVerificationCode;

interface UserPhoneNumberVerificationService
{
    /**
     * Verifies the phone number ownership
     */
    public function doesVerificationCodePassOwnershipVerification(
        PhoneNumber $phoneNumber,
        UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode
    ): bool;

    public function sendPhoneNumberVerificationCode(PhoneNumber $phoneNumber): void;
}
