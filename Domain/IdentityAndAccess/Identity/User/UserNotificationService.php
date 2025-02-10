<?php
namespace domain\identityAndAccess\identity\user;

use Domain\IdentityAndAccess\Identity\User\Events\ResendEmailVerificationCodeRequested;
use Domain\IdentityAndAccess\Identity\User\Events\UserEmailAddressChanged;
use Domain\IdentityAndAccess\Identity\User\Events\UserRegistered;
use Domain\IdentityAndAccess\Identity\User\Events\NewPasswordChangeCodeRequested;

interface UserNotificationService {
    /**
     * @param UserRegistered $userRegistered
     * @return void
     */
    public function sendByEmailRegistrationVerificationWhen(UserRegistered $userRegistered);

    /**
     * @param ResendEmailVerificationCodeRequested $resendRequested
     * @return void
     */
    public function resendEmailVerificationWhenRequested(
            ResendEmailVerificationCodeRequested $resendRequested);

    /**
     * @param NewPasswordChangeCodeRequested $newPassRequested
     * @return void
     */
    public function sendPasswordChangeCodeViaEmailWhenRequested(
            NewPasswordChangeCodeRequested $newPassRequested);

    /**
     * @param NewPasswordChangeCodeRequested $newPassRequested
     * @return void
     */
    public function sendPasswordChangeCodeViaSmsWhenRequested(
            NewPasswordChangeCodeRequested $newPassRequested);

    /**
     * @param UserEmailAddressChanged $emailChanged
     * @return void
     */
    public function sendEmailVerificationWhenEmailChanged(
            UserEmailAddressChanged $emailChanged);
}

?>
