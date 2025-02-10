<?php

namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\AbstractId;

class UserAuthenticationAttemptId extends AbstractId
{
    protected function setId($id)
    {
        if (is_int($id) || (is_string($id) && ctype_digit($id))) {
            $this->id = (int)$id;
        } else {
            throw new \DomainException('Could not set user authenticated attempt id. Invalid id.');
        }
    }
}
