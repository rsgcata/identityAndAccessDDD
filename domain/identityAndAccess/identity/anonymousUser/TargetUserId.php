<?php
namespace domain\identityAndAccess\identity\anonymousUser;

use domain\identityAndAccess\identity\user\UserId;

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
class TargetUserId extends UserId {
	protected function setId($id) {
		if($id === NULL) {
			$this->id = $id;
		}
		else if(is_int($id) || (is_string($id) && ctype_digit($id))) {
			$this->id = (int) $id;
		}
		else {
			throw new \DomainException('Could not set target user id. Invalid id.');
		}
	}
}

?>
