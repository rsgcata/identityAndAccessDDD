<?php
namespace domain\identityAndAccess\identity\user;

use Domain\Contact\PhoneNumber;
use Domain\IdentityAndAccess\Identity\User\ActionAuthenticity\UserPhoneNumberVerificationCode;

interface IUserPhoneNumberVerificationService {
    /**
     * Verifies the phone number ownership
     *
     * @param PhoneNumber $phoneNumber
     * @param UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode
     * @return bool
     */
    public function doesVerificationCodePassOwnershipVerification(
            PhoneNumber $phoneNumber,
            UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode);

    /**
     * @param PhoneNumber $phoneNumber
     * @return void
     */
    public function sendPhoneNumberVerificationCode(PhoneNumber $phoneNumber);
}

?>
