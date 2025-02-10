<?php
namespace Domain\IdentityAndAccess\Identity\User;

use Domain\IdentityAndAccess\Identity\User\Events\NewPasswordChangeCodeRequested;
use Domain\IdentityAndAccess\Identity\User\Events\ResendEmailVerificationCodeRequested;
use Domain\IdentityAndAccess\Identity\User\Events\UserEmailAddressChanged;
use Domain\IdentityAndAccess\Identity\User\Events\UserRegistered;

interface UserNotificationService
{
    public function sendByEmailRegistrationVerificationWhen(UserRegistered $userRegistered): void;

    public function resendEmailVerificationWhenRequested(
        ResendEmailVerificationCodeRequested $resendRequested
    ): void;

    public function sendPasswordChangeCodeViaEmailWhenRequested(
        NewPasswordChangeCodeRequested $newPassRequested
    ): void;

    public function sendPasswordChangeCodeViaSmsWhenRequested(
        NewPasswordChangeCodeRequested $newPassRequested
    ): void;

    public function sendEmailVerificationWhenEmailChanged(
        UserEmailAddressChanged $emailChanged
    ): void;
}

?>
