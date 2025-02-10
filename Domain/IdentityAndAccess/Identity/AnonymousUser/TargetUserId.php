<?php

namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\IdentityAndAccess\Identity\User\UserId;
use DomainException;

class TargetUserId extends UserId
{
    protected function setId($id): void
    {
        if ($id === null) {
            $this->id = $id;
        } else if (is_int($id) || (is_string($id) && ctype_digit($id))) {
            $this->id = (int)$id;
        } else {
            throw new DomainException('Could not set target user id. Invalid id.');
        }
    }
}
