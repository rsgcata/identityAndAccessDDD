<?php
namespace domain\identityAndAccess\identity\anonymousUser;

use common\domain\AbstractId;

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
class UserAuthenticationAttemptId extends AbstractId {
	protected function setId($id) {
		if(is_int($id) || (is_string($id) && ctype_digit($id))) {
			$this->id = (int) $id;
		}
		else {
			throw new \DomainException('Could not set user authenticated attempt id. Invalid id.');
		}
	}
}

?>
