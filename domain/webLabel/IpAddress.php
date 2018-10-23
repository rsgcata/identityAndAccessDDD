<?php
namespace domain\webLabel;

use \common\domain\AbstractValueObject;

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
class IpAddress extends AbstractValueObject{
    /**
     * The internet protocol address
     *
     * @var string|null
     * @access protected
     */
    protected $ipAddress;
    
    protected function __construct() {
    }
    
    /**
     * Create a new ip address value object
     * 
     * @param string|null $ipAddress The ip address
     *
     * @return static
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($ipAddress) {
        $self = new static();        
        $self->setIpAddress($ipAddress);
        return $self;
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param IpAddress $ipAddress
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(IpAddress $ipAddress) {
        $equalObjects = FALSE;
        
        if(static::class === get_class($ipAddress)
                && $this->ipAddress === $ipAddress->getIpAddress()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * @return string|null
     */
    public function getIpAddress() {
        return $this->ipAddress;
    }

    protected function setIpAddress($ipAddress) {
        if($ipAddress !== NULL) {
            if(is_string($ipAddress)) {
                $ipAddress = trim($ipAddress);
            }
            
            if($ipAddress === '') {
                $ipAddress = NULL;
            }
            else {
                $this->assert()->ipAddress($ipAddress, new \DomainException('Invalid generic ip address.'
                        . ' Could not set the ip address.'));
            }
        }
        $this->ipAddress = $ipAddress;
    }

    /**
     * 
     * @return static
     */
    public static function reconstitute($ipAddress) {
        $self = new static();
        $self->ipAddress = $ipAddress;
        return $self;
    }

}

?>
