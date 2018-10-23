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
class UserPhoneNumberManagementService {
    /**
     * Short description
     *
     * @var IUserRepository
     * @access private
     */
    private $userRepository;
    
    /**
     * Short description
     *
     * @var IUserPhoneNumberVerificationService
     * @access private
     */
    private $userPhoneNumberVerificationService;
    
    public function __construct(
            IUserRepository $userRepository, 
            IUserPhoneNumberVerificationService $userPhoneNumberVerificationService) {
        $this->userRepository = $userRepository;
        $this->userPhoneNumberVerificationService = $userPhoneNumberVerificationService;
    }

    /**
     * Adds and verifies a phone number
     * 
     * @param User $user
     * @param PhoneNumber $phoneNumber
     * @param UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode
     * 
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function addNewUserPhoneNumber(
            User $user, 
            PhoneNumber $phoneNumber, 
            UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode) {        
        $ownershipVerificationPassed = $this->userPhoneNumberVerificationService
                ->doesVerificationCodePassOwnershipVerification(
                        $phoneNumber, 
                        $userPhoneNumberVerificationCode);
        
        if(!$ownershipVerificationPassed) {
            throw new \DomainException('Could not add new user phone number. The ownership verification'
                    . ' failed.');
        }
        
        $user->addPhoneNumber($phoneNumber);
        $user->markPhoneNumberOwnershipAsVerified($phoneNumber);
        
        $this->userRepository->saveModificationsFor($user);
    }
    
    /**
     * Send the verification code to the phone number
     * 
     * @param User $user
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function sendPhoneNumberVerificationCode(
            User $user, 
            PhoneNumber $phoneNumber) {
        $user->sendPhoneNumberVerificationCode();
        $this->userPhoneNumberVerificationService->sendPhoneNumberVerificationCode($phoneNumber);
    }
}

?>
