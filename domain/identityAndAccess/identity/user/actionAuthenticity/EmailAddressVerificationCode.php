<?php
namespace domain\identityAndAccess\identity\user\actionAuthenticity;

use common\domain\AbstractValueObject;

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
class EmailAddressVerificationCode extends AbstractValueObject{
    /**
     * The verification code
     *
     * @var string
     * @access private
     */
    private $verificationCode;
    
    /**
     * The verification code
     *
     * @var string
     * @access private
     */
    private $hashedVerificationCode;
    
    private function __construct() {
    }
    
    /**
     * Create new email address verification code value object
     * 
     * @param string $code The verification code
     *
     * @return EmailAddressVerificationCode
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($code) {
        $self = new self();
        $self->setVerificationCode($code);
        $self->hashedVerificationCode = $self->protectCode($self->verificationCode);
        return $self;
    }
    
    /**
     * Auto generate email address verification code
     *
     * @return EmailAddressVerificationCode
     * @throws --
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function autoGenerateValidCode() {
        $self = new self();
        $self->verificationCode = $self->generateVerificationCode();
        $self->hashedVerificationCode = $self->protectCode($self->verificationCode);
        return $self;
    }
    
    /**
     * Generates a 256 characters long string code
     *
     * @return string
     * @throws \Exception If the random bytes function could not generate the random bytes
     *
     * @access private
     * @since Method/function available since Release 1.0
     */
    private function generateVerificationCode() {
        $initialStrongString = base64_encode(random_bytes(300));
        $code = mb_substr(preg_replace("/[^a-zA-Z0-9]+/u", "", $initialStrongString),0,256);
        
        return $code;
    }
    
    /**
     * @param string $code The plain code
     *
     * @return string
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    private function protectCode($code) {
        return password_hash($code, PASSWORD_DEFAULT);
    }
    
    /**
     * Check if this code equals a given code
     * 
     * @param string $code The code to be conmapred with
     *
     * @return bool
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function codeEquals($code) {
        return password_verify($code, $this->hashedVerificationCode);
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param EmailAddressVerificationCode $emailAddressVerificationCode
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(EmailAddressVerificationCode $emailAddressVerificationCode) {
        $equalObjects = FALSE;
        
        if(self::class === get_class($emailAddressVerificationCode)
                && $this->hashedVerificationCode 
                === $emailAddressVerificationCode->getHashedVerificationCode()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * 
     * @return string
     */
    public function getVerificationCode() {
        return $this->verificationCode;
    }
    
    /**
     * 
     * @return string
     */
    public function getHashedVerificationCode() {
        return $this->hashedVerificationCode;
    }

    private function setVerificationCode($verificationCode) {
        if(!is_string($verificationCode) 
                || preg_match('/^[a-z0-9]{256}$/i', $verificationCode) !== 1) {
            throw new \DomainException('Could not set email address verification code. Invalid code.');
        }
        
        $this->verificationCode = $verificationCode;
    }

    /**
     * @return EmailAddressVerificationCode
     */
    public static function reconstitute($hashedVerificationCode) {
        $self = new self();
        $self->hashedVerificationCode = $hashedVerificationCode;
        return $self;
    }

}

?>
