<?php
namespace domain\identityAndAccess\identity\user;

use common\domain\AbstractDomainObject;
use common\domain\DomainEventPublisher;
use domain\contact\FullName;
use domain\contact\PhoneNumber;
use domain\identityAndAccess\identity\user\events\NewPasswordChangeCodeRequested;
use domain\identityAndAccess\identity\user\events\ResendEmailVerificationCodeRequested;
use domain\identityAndAccess\identity\user\events\UserEmailAddressChanged;
use domain\identityAndAccess\identity\user\events\UserRegistered;
use domain\identityAndAccess\identity\user\actionAuthenticity\PasswordChangeCode;
use domain\identityAndAccess\identity\user\actionAuthenticity\EmailAddressVerificationCode;

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
class User extends AbstractDomainObject{
    /**
     * User id
     *
     * @var UserId
     * @access private
     */
    private $id;
    
    /**
     * Email Address
     *
     * @var UserEmailAddress
     * @access private
     */
    private $emailAddress;
    
    /**
     * Email address verification code
     *
     * @var AccountModificationRequestCode
     * @access private
     */
    private $emailAddressVerificationCode;
    
    /**
     * Password
     *
     * @var string
     * @access private
     */
    private $password;
    
    /**
     * The code used to change the password
     *
     * @var AccountModificationRequestCode
     * @access private
     */
    private $passwordChangeCode;
    
    /**
     * The person as tenant of the user
     *
     * @var Person
     * @access private
     */
    private $person;
    
    /**
     * Email address ownership verification flag
     *
     * @var boolean
     * @access private
     */
    private $emailAddressOwnershipVerified;
    
    /**
     * The time and date when the user registered
     *
     * @var \DateTime
     * @access private
     */
    private $registrationDate;

    private function __construct() {
    }
    
    /**
     * Register a new user
     * 
     * @param UserEmailAddress $emailAddress The email address
     * @param Person $person The person 
     * @param string $password The password
     * @param IEncryptionService $encryptionService
     *
     * @return User
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function registerUser(UserEmailAddress $emailAddress, Person $person, $password, 
            IEncryptionService $encryptionService) {
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
        
        DomainEventPublisher::getInstance()->publish(new UserRegistered($self));
        
        return $self;
    }
    
    /**
     * Resend the email verification code
     *
     * @return void
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function resendEmailVerificationCode() {
        if($this->hasVerifiedEmailAddress()) {
            throw new \DomainException('Could not resend email verification code.'
                    . ' The email is already verified.');
        }
        
        $this->setEmailAddressVerificationCode(EmailAddressVerificationCode::autoGenerateValidCode());
        
        DomainEventPublisher::getInstance()->publish(new ResendEmailVerificationCodeRequested($this));
    }
    
    /**
     * Protect password
     * 
     * @param string $password The user password to protect
     * @param IEncryptionService $encryptionService
     *
     * @return string The protected/encrypted password
     * @throws \DomainException
     *
     * @access private
     * @since Method/function available since Release 1.0
     */
    private function protectPassword($password, IEncryptionService $encryptionService) {
        $this->assert()->stringCharCount($password, 8, 256, new \DomainException('The user password does ' 
            . 'not fulfill the requirements of a strong password. It requries minimum 8 characters.'));
        
        if(preg_match('/\p{Ll}+/u', $password) !== 1 
                || preg_match('/\p{Lu}+/u', $password) !== 1
                || preg_match('/\p{N}+/u', $password) !== 1
                || preg_match('/\p{P}+/u', $password) !== 1) {            
            throw new \DomainException('The user password does not fulfill the requirements of a ' 
                    . 'strong password! It requires at least one of each of the following charaters : ' 
                    . 'an uppercase letter, a lowercase letter, a number and a punctuation mark.');        
        }
        
        return $encryptionService->hashUserPassword($password);
    }
    
    /**
     * Verify and validate email address ownership
     * 
     * @param EmailAddressVerificationCode $code The email address verification code to be validated
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function verifyEmailAddressOwnership(EmailAddressVerificationCode $code) {
        if(!$this->emailAddressVerificationCode->codeEquals($code->getVerificationCode())) {
            throw new \DomainException('The email address could not be verified! Invalid code!');
        }
        
        $this->emailAddressOwnershipVerified = TRUE;
    }
    
    /**
     * Expose if the user has email address verified
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function hasVerifiedEmailAddress() {
        return $this->emailAddressOwnershipVerified;
    }
    
    /**
     * Change the full name
     * 
     * @param FullName $fullName
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeFullName(FullName $fullName) {
        $this->person->changeFullName($fullName);
    }
    
    /**
     * Change the address
     * 
     * @param UserAddress $address
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeAddress(UserAddress $address) {
        $this->person->changeAddress($address);
    }
    
    /**
     * Add a phone number
     * 
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function addPhoneNumber(PhoneNumber $phoneNumber) {
        if(!$this->emailAddressOwnershipVerified) {
            throw new \DomainException('Could not add phone number.'
                    . ' The email address ownership has not been verified yet.');
        }
        
        if(count($this->person->getUserPhoneNumbers()) === Person::MAX_PHONE_NUMBERS) {
            throw new \DomainException('Could not add phone number. The max phone numbers limit'
                    . ' has been reached.');
        }
        
        $this->person->addNewUserPhoneNumber($phoneNumber);
    }
    
    /**
     * Send the phone number verification code. Since the code should be sent before adding a phone number,
     * there should be checked if a new phone can be added (max count check). If code should be sent
     * after the phone number was added, the count check should be removed
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function sendPhoneNumberVerificationCode() {
        if(!$this->emailAddressOwnershipVerified) {
            throw new \DomainException('Could not send phone number verification code.'
                    . ' The email address ownership has not been verified yet.');
        }
        
        if(count($this->person->getUserPhoneNumbers()) === Person::MAX_PHONE_NUMBERS) {
            throw new \DomainException('Could not send phone number verification code.'
                    . ' The max phone numbers limit has been reached.');
        }
    }
    
    /**
     * Remove a phone number
     * 
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function removePhoneNumber(PhoneNumber $phoneNumber) {        
        $this->person->removePhoneNumber($phoneNumber);
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function markPhoneNumberOwnershipAsVerified(PhoneNumber $phoneNumber) {        
        $this->person->markPhoneNumberOwnershipAsVerified($phoneNumber);
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function markPhoneNumberAsPrimary(PhoneNumber $phoneNumber) {
        $this->person->markPhoneNumberAsPrimary($phoneNumber);
    }
    
    /**
     * Change the phone number
     * 
     * @param UserEmailAddress $emailAddress
     * @param string $plainPassword
     * @param IEncryptionService $encryptionService
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeEmailAddress(UserEmailAddress $emailAddress, $plainPassword,
            IEncryptionService $encryptionService) {
        if(!$encryptionService->plainUserPasswordMatchingHashedPassword(
                $plainPassword, $this->password)) {
            throw new \DomainException('Could not change the email address. Password missmatch.');
        }
        
        $this->setEmailAddressOwnershipVerified(FALSE);
        $this->setEmailAddressverificationCode(
                EmailAddressVerificationCode::autoGenerateValidCode());
        $this->setEmailAddress($emailAddress);
        
        DomainEventPublisher::getInstance()->publish(new UserEmailAddressChanged($this));
    }
    
    /**
     * Set's / generates a new password change code as requested
     *
     * @return void
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function requestNewPasswordChangeCode() {
        $this->setPasswordChangeCode(PasswordChangeCode::autoGenerateValidCode());
        DomainEventPublisher::getInstance()->publish(new NewPasswordChangeCodeRequested($this));
    }
    
    /**
     * Change the phone number
     * 
     * @param string $passwordChangeCode
     * @param string $plainNewPassword
     * @param IEncryptionService $encryptionService
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeForgottenPassword(
            $passwordChangeCode, 
            $plainNewPassword,
            IEncryptionService $encryptionService) {
        if($this->passwordChangeCode->isExpired()) {
            throw new \DomainException('Could not change the forgotten password. Password change'
                    . ' code is expired.');
        }
        
        if(!$this->passwordChangeCode->codeEquals($passwordChangeCode)) {
            throw new \DomainException('Could not change the forgotten password. Password change'
                    . ' code missmatch.');
        }
        
        $this->password = $this->protectPassword($plainNewPassword, $encryptionService);
        $this->setPasswordChangeCode(PasswordChangeCode::autoGenerateExpiredCode());
    }
    
    /**
     * Change known password
     * 
     * @param string $plainNewPassword
     * @param string $plainOldPassword
     * @param IEncryptionService $encryptionService
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeKnownPassword(
            $plainNewPassword,
            $plainOldPassword,
            IEncryptionService $encryptionService) {
        if(!$encryptionService->plainUserPasswordMatchingHashedPassword(
                $plainOldPassword, $this->password)) {
            throw new \DomainException('Could not change the known password. Password missmatch.');
        }
        
        $this->password = $this->protectPassword($plainNewPassword, $encryptionService);
    }
    
    /**
     * Deletes the user account
     * 
     * @param string $password
     * @param IEncryptionService $encryptionService
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function deleteAccount(
            $password,
            IEncryptionService $encryptionService) {
        // This condition is needed so bad intended people can't create accounts with random emails
        // and after delete them so the real email owner can't use it anymore
        if(!$this->emailAddressOwnershipVerified) {
            throw new \DomainException('Could not delete user account.'
                    . ' The email address is not verified.');
        }
        
        if(!$encryptionService->plainUserPasswordMatchingHashedPassword(
                $password, $this->password)) {
            throw new \DomainException('Could not delete user account. Password missmatch.');
        }
    }
    
    /**
     * 
     * @return UserId
     */
    public function getId() {
        return $this->id;
    }

    /**
     * 
     * @return UserEmailAddress
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @return EmailAddressVerificationCode
     */
    public function getEmailAddressverificationCode() {
        return $this->emailAddressVerificationCode;
    }
    
    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
    
    /**
     * 
     * @return PasswordChangeCode
     */
    public function getPasswordChangeCode() {
        return $this->passwordChangeCode;
    }

    /**
     * @return Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * @return boolean
     */
    public function getEmailAddressOwnershipVerified() {
        return $this->emailAddressOwnershipVerified;
    }
        
    /**
     * @return \DateTime
     */
    public function getRegistrationDate() {
        return $this->registrationDate;
    }
    
    private function setEmailAddress(UserEmailAddress $emailAddress) {
        $this->emailAddress = $emailAddress;
    }

    private function setEmailAddressVerificationCode(
            EmailAddressVerificationCode $emailAddressVerificationCode) {
        $this->emailAddressVerificationCode = $emailAddressVerificationCode;
    }

    private function setPerson(Person $person) {
        $this->person = $person;
    }

    private function setEmailAddressOwnershipVerified($emailAddressOwnershipVerified) {
        if(!is_bool($emailAddressOwnershipVerified)) {
            throw new \DomainException('Could not set email address ownership verified to user. Should'
                    . ' be boolean.');
        }
        
        $this->emailAddressOwnershipVerified = $emailAddressOwnershipVerified;
    }
    
    private function setPasswordChangeCode(PasswordChangeCode $passwordChangeCode) {
        $this->passwordChangeCode = $passwordChangeCode;
    }

    /**
     * 
     * @return User
     */
    public static function reconstitute(
            UserId $userId, 
            UserEmailAddress $emailAddress, 
            EmailAddressVerificationCode $emailAddressVerificationCode, 
            $password, 
            PasswordChangeCode $passwordChangeCode,
            Person $person, 
            $emailAddressOwnershipVerified,
            \DateTime $registrationDate) {
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

?>
