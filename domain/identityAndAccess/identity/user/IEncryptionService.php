<?php
namespace domain\identityAndAccess\identity\user;
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
interface IEncryptionService {
	/**
	 * @param string $password The password to be hashed
	 * @return string The hashed version of the password
	 */
	public function hashUserPassword($password);
	
	/**
	 * Verify a plain user password against a hashed user password
	 * 
	 * @param string $plainUserPassword
	 * @param string $hashedUserPassword
	 * 
	 * @return boolean
	 */
	public function plainUserPasswordMatchingHashedPassword($plainUserPassword, $hashedUserPassword);
}

?>
