<?php
namespace domain\identityAndAccess\identity\user\actionAuthenticity;

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
class PasswordChangeCode extends AbstractActionAuthenticityCode {	
	/**
	 * Auto generate password change valid code
	 *
	 * @return PasswordChangeCode
	 * @throws --
	 *
	 * @static
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function autoGenerateValidCode() {
		$self = new self();
		$self->code = $self->generateCode();
		$self->hashedCode = $self->protectCode($self->code);
		$self->setExpirationDateTime((new \DateTime())->add(new \DateInterval('PT10M')));
		return $self;
	}
	
	/**
	 * Generates a 9 characters long string code
	 *
	 * @return string
	 * @throws --
	 *
	 * @access protected
	 * @since Method/function available since Release 1.0
	 */
	protected function generateCode() {
		$initialStrongString = base64_encode(random_bytes(32));
		$code = mb_strtolower(mb_substr(preg_replace("/[^a-zA-Z0-9]+/u", "", $initialStrongString),0,9));
		return $code;
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
	public function codeEquals($code) {
		$code = str_replace(' ', '', mb_strtolower($code));
		return password_verify($code, $this->hashedCode);
	}

	protected function setCode($code) {
		if(is_string($code)) {
			$code = str_replace(' ', '', mb_strtolower($code));
			
			if(preg_match('/^[a-z0-9]{9}$/i', $code) !== 1) {
				throw new \DomainException('Could not set password change code. Invalid code.');
			}
		}
		else {
			throw new \DomainException('Could not set password change code. Invalid code.');
		}
		
		$this->code = $code;
	}

}

?>

