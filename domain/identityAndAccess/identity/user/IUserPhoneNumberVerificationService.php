<?php
namespace domain\identityAndAccess\identity\user;

use domain\contact\PhoneNumber;
use domain\identityAndAccess\identity\user\actionAuthenticity\UserPhoneNumberVerificationCode;

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
