<?php
namespace domain\identityAndAccess\identity\anonymousUser;

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
class AuthenticationDetailsStatus extends AbstractValueObject {
    /**
     * Pending details attachment
     *
     * @static
     * @var int
     * @access public
     */
    const PENDING_DETAILS_ATTACHMENT = 1;
    
    /**
     * The user agent details are attached but the ip address details need an attachment retry
     *
     * @static
     * @var int
     * @access public
     */
    const PENDING_IP_DETAILS_ATTACHMENT_RETRY = 2;
    
    /**
     * The attachment of all the details is finalized
     *
     * @static
     * @var int
     * @access public
     */
    const DETAILS_ATTACHMENT_FINALIZED = 3;
    
    /**
     * The actual status
     *
     * @var int
     * @access private
     */
    private $status;
    
    /**
     * Create new status object
     * 
     * @param int $status
     *
     * @return AuthenticationDetailsStatus
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function createNew($status) {
        $self = new self();
        $self->setStatus($status);
        return $self;
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param AuthenticationDetailsStatus $status
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(AuthenticationDetailsStatus $status) {
        $equalObjects = FALSE;
        
        if(self::class === get_class($status)
                && $this->status === $status->getStatus()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * 
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    private function setStatus($status) {
        if(!in_array($status, 
                array(
                    self::DETAILS_ATTACHMENT_FINALIZED,
                    self::PENDING_DETAILS_ATTACHMENT,
                    self::PENDING_IP_DETAILS_ATTACHMENT_RETRY
                ), 
                TRUE)) {
            throw new \DomainException('Could not set the status to authentication details status.'
                    . ' Invalid status.');
        }
        
        $this->status = $status;
    }

    /**
     * @return AuthenticationDetailsStatus
     */
    public static function reconstitute($status) {
        $self = new self();
        $self->status = $status;
        return $self;
    }
}

?>
