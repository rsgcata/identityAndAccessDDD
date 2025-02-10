<?php
namespace domain\identityAndAccess\access\representation;

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
class AuthenticationRep {
    /**
     * Was it a successful authentication?
     *
     * @var boolean
     * @access private
     */
    private $successfulAuthentication;
    
    /**
     * Invalid credentials?
     *
     * @var boolean
     * @access private
     */
    private $authenticatedWithInvalidCredentials;
    
    /**
     * Authentication locked?
     *
     * @var boolean
     * @access private
     */
    private $authenticationLocked;
    
    /**
     * Short description
     *
     * @var \DateTime
     * @access private
     */
    private $authenticationLockExpiration;
    
    /**
     * Constructor
     * 
     * @param boolean $successfulAuthentication
     * @param boolean $authenticatedWithInvalidCredentials
     * @param boolean $authenticationLocked
     * @param \DateTime $authenticationLockExpiration
     *
     * @return AuthenticationRep
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function __construct($successfulAuthentication, $authenticatedWithInvalidCredentials, 
            $authenticationLocked,
            \DateTime $authenticationLockExpiration = NULL) {
        $this->successfulAuthentication = $successfulAuthentication;
        $this->authenticatedWithInvalidCredentials = $authenticatedWithInvalidCredentials;
        $this->authenticationLocked = $authenticationLocked;
        $this->authenticationLockExpiration = $authenticationLockExpiration !== NULL
                ? $authenticationLockExpiration : new \DateTime();
    }
    
    public function getSuccessfulAuthentication() {
        return $this->successfulAuthentication;
    }

    public function getAuthenticatedWithInvalidCredentials() {
        return $this->authenticatedWithInvalidCredentials;
    }

    public function getAuthenticationLocked() {
        return $this->authenticationLocked;
    }
    
    /**
     * 
     * @return \DateTime
     */
    public function getAuthenticationLockExpiration(){
        return $this->authenticationLockExpiration;
    }
}

?>
