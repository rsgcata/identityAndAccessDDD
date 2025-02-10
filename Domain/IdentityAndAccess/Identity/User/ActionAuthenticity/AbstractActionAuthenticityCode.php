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
abstract class AbstractActionAuthenticityCode extends AbstractValueObject {
    /**
     * The hashed code
     *
     * @var string
     * @access protected
     */
    protected $hashedCode;
    
    /**
     * The code
     *
     * @var string
     * @access protected
     */
    protected $code;
    
    /**
     * The date and time when the code will expire
     *
     * @var \DateTime
     * @access protected
     */
    protected $expirationDateTime;
    
    protected function __construct() {
    }
    
    /**
     * Create value object
     * 
     * @param string $code The plain code
     *
     * @return static
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($code) {
        $self = new static();
        $self->setCode($code);
        $self->hashedCode = $self->protectCode($self->code);
        $self->setExpirationDateTime(new \DateTime());
        return $self;
    }
    
    /**
     * Auto generate expired code
     *
     * @return static
     * @throws --
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function autoGenerateExpiredCode() {
        $self = new static();
        $self->code = $self->generateCode();
        $self->hashedCode = $self->protectCode($self->code);
        $self->setExpirationDateTime(new \DateTime('2000-01-01'));
        return $self;
    }
    
    /**
     * Generates a string code
     *
     * @return string
     * @throws --
     *
     * @access protected
     * @since Method/function available since Release 1.0
     */
    abstract protected function generateCode();
    
    /**
     * @param string $code The plain code
     *
     * @return string
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    protected function protectCode($code) {
        return password_hash($code, PASSWORD_DEFAULT);
    }
    
    /**
     * Check if this code equals a given code
     * 
     * @param string $code The code to be compared with
     *
     * @return bool
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    abstract public function codeEquals($code);
    
    /**
     * Check if this object equals another object
     * 
     * @param AbstractActionAuthenticityCode $actionAuthenticityCode
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(AbstractActionAuthenticityCode $actionAuthenticityCode) {
        $equalObjects = FALSE;
        
        if(static::class === get_class($actionAuthenticityCode)
                && $this->hashedCode === $actionAuthenticityCode->getHashedCode()
                && $this->expirationDateTime->getTimestamp() 
                === $actionAuthenticityCode->getExpirationDateTime()->getTimestamp()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * Checks if the code is expired
     *
     * @return bool
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function isExpired() {
        if($this->expirationDateTime->getTimestamp() < (new \DateTime())->getTimestamp()) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * 
     * @return string
     */
    public function getCode() {
        return $this->code;
    }
    
    /**
     * 
     * @return string
     */
    public function getHashedCode() {
        return $this->hashedCode;
    }    
    
    /**
     * 
     * @return \DateTime
     */
    public function getExpirationDateTime() {
        return $this->expirationDateTime;
    }

    abstract protected function setCode($code);
    
    protected function setExpirationDateTime(\DateTime $expirationDateTime) {        
        $this->expirationDateTime = $expirationDateTime;
    }
    
    /**
     * @param string $hashedCode
     * @param \DateTime $expirationDateTime
     * @return static
     */
    public static function reconstitute($hashedCode, \DateTime $expirationDateTime) {
        $self = new static();
        $self->hashedCode = $hashedCode;
        $self->expirationDateTime = $expirationDateTime;
        return $self;
    }

}

?>

