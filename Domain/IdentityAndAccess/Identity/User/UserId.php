<?php

namespace Domain\IdentityAndAccess\Identity\User;

use Domain\AbstractId;

class UserId extends AbstractId {
    protected function setId($id) {
        if(is_int($id) || (is_string($id) && ctype_digit($id))) {
            $this->id = (int) $id;
        }
        else {
            throw new \DomainException('Could not set user id. Invalid id.');
        }
    }
}
