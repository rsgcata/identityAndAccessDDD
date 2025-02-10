<?php
namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use DomainException;

class AuthenticationDetailsStatus
{
    public const int PENDING_DETAILS_ATTACHMENT = 1;
    public const int PENDING_IP_DETAILS_ATTACHMENT_RETRY = 2;
    public const int DETAILS_ATTACHMENT_FINALIZED = 3;

    private int $status;

    /**
     * @throws DomainException
     */
    public static function createNew(int $status): self
    {
        $self = new self();
        $self->setStatus($status);
        return $self;
    }

    public function equals(AuthenticationDetailsStatus $status): bool
    {
        return self::class === get_class($status) && $this->status === $status->getStatus();
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    private function setStatus($status): void
    {
        if (!in_array(
            $status,
            array(
                self::DETAILS_ATTACHMENT_FINALIZED,
                self::PENDING_DETAILS_ATTACHMENT,
                self::PENDING_IP_DETAILS_ATTACHMENT_RETRY
            ),
            true
        )) {
            throw new DomainException(
                'Could not set the status to authentication details status. Invalid status.'
            );
        }

        $this->status = $status;
    }

    public static function reconstitute($status): AuthenticationDetailsStatus
    {
        $self = new self();
        $self->status = $status;
        return $self;
    }
}
