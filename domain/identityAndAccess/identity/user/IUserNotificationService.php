<?php
namespace domain\identityAndAccess\identity\user;

use domain\identityAndAccess\identity\user\events\ResendEmailVerificationCodeRequested;
use domain\identityAndAccess\identity\user\events\UserEmailAddressChanged;
use domain\identityAndAccess\identity\user\events\UserRegistered;
use domain\identityAndAccess\identity\user\events\NewPasswordChangeCodeRequested;

/**
 *
 * Short description 
 *
 * Long description 
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
interface IUserNotificationService {
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
