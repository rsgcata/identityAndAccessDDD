<?php

namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use DateTime;
use Domain\WebLabel\IpAddress;
use DomainException;

/**
 * Short description
 * Long description
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
class UserAuthenticationAttempt
{
    private UserAuthenticationAttemptId $id;
    private IpAddress $ipAddress;
    private DateTime $dateOfAttempt;
    private TargetUserId $targetUserId;
    private bool $attemptSucceeded;
    private string $userAgent;
    private string $referer;
    private AuthenticationDetailsStatus $detailsStatus;
    private string $countryCode;
    private string $state;
    private string $city;
    private string $os;
    private string $browserName;
    private int $numberOfDetailsAttachmentTries;
    private bool $suspiciousAuthentication;

    const int AUTHENTICATION_LOCK_SECONDS_TO_LIVE = 120;
    const int AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS = 5;
    const int MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES = 3;

    private function __construct()
    {

    }

    /**
     * Create new authentication attempt
     *
     * @throws DomainException
     */
    public static function create(
        IpAddress    $ipAddress,
        bool         $succeeded,
        TargetUserId $targetUserId,
        string       $userAgent,
        string       $referer
    ): self
    {
        $self = new self();
        $self->setIpAddress($ipAddress);
        $self->setDetailsStatus(
            AuthenticationDetailsStatus::createNew(
                AuthenticationDetailsStatus::PENDING_DETAILS_ATTACHMENT
            )
        );
        $self->setTargetUserId($targetUserId);
        $self->setAttemptSucceeded($succeeded);
        $self->setDateOfAttempt(new DateTime());
        $self->setReferer($referer);
        $self->setUserAgent($userAgent);
        $self->setCountryCode(null);
        $self->setCity(null);
        $self->setState(null);
        $self->setOs(null);
        $self->setBrowserName(null);
        $self->id = UserAuthenticationAttemptId::createnull();
        $self->setNumberOfDetailsAttachmentTries(0);
        $self->setSuspiciousAuthentication(false);

        return $self;
    }

    public function fillAuthenticationDetails(
        string                      $countryCode,
        string                      $state,
        string                      $city,
        string                      $os,
        string                      $browserName,
        AuthenticationDetailsStatus $detailsStatus
    ): void
    {
        if (
            $this->detailsStatus->getStatus() ===
            AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED
        ) {
            throw new DomainException(
                'Could not fill authentication details for user authentication'
                . ' attempt. The current details status is not right for this action.'
            );
        }

        if ($this->numberOfDetailsAttachmentTries >= self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES) {
            throw new DomainException(
                'Could not fill authentication details for user authentication'
                . ' attempt. The max number of attachment tries has already been reached.'
            );
        }

        if ($detailsStatus->getStatus()
            !== AuthenticationDetailsStatus::PENDING_IP_DETAILS_ATTACHMENT_RETRY
            && $detailsStatus->getStatus()
            !== AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
            throw new DomainException(
                'Could not fill authentication details for user authentication'
                . ' attempt. The details status provided is not right for this action.'
            );
        }

        $this->setNumberOfDetailsAttachmentTries($this->numberOfDetailsAttachmentTries + 1);

        $this->setBrowserName($browserName);
        $this->setOs($os);
        $this->setCountryCode($countryCode);
        $this->setCity($city);
        $this->setState($state);

        if ($this->numberOfDetailsAttachmentTries === self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES
            || ($this->countryCode !== null && $this->os !== null && $this->browserName !== null)) {
            $detailsStatus = AuthenticationDetailsStatus::createNew(
                AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED
            );
        }

        $this->setDetailsStatus($detailsStatus);
    }

    public function tryRefillingIpDetails(
        string                      $countryCode,
        string                      $state,
        string                      $city,
        AuthenticationDetailsStatus $detailsStatus
    ): void
    {
        if (
            $this->detailsStatus->getStatus() ===
            AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED
        ) {
            throw new DomainException(
                'Could not refill ip details for user authentication'
                . ' attempt. The current details status is not right for this action.'
            );
        }

        if ($this->numberOfDetailsAttachmentTries >= self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES) {
            throw new DomainException(
                'Could not refill ip details for user authentication'
                . ' attempt. The max number of attachment tries has already been reached.');
        }

        if ($detailsStatus->getStatus()
            !== AuthenticationDetailsStatus::PENDING_IP_DETAILS_ATTACHMENT_RETRY
            && $detailsStatus->getStatus()
            !== AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
            throw new DomainException(
                'Could not refill ip details for user authentication'
                . ' attempt. The details status provided is not right for this action.');
        }

        $this->setNumberOfDetailsAttachmentTries($this->numberOfDetailsAttachmentTries + 1);

        $this->setCountryCode($countryCode);
        $this->setCity($city);
        $this->setState($state);

        if ($this->numberOfDetailsAttachmentTries === self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES
            || $this->countryCode !== null) {
            $detailsStatus = AuthenticationDetailsStatus::createNew(
                AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED);
        }

        $this->setDetailsStatus($detailsStatus);
    }

    public function isSuccessful(): bool
    {
        return $this->attemptSucceeded;
    }

    public function isSuspiciousAuthentication(): bool
    {
        return $this->suspiciousAuthentication;
    }

    public function getId(): UserAuthenticationAttemptId
    {
        return $this->id;
    }

    public function getIpAddress(): IpAddress
    {
        return $this->ipAddress;
    }

    public function getDetailsStatus(): AuthenticationDetailsStatus
    {
        return $this->detailsStatus;
    }

    public function getDateOfAttempt(): DateTime
    {
        return $this->dateOfAttempt;
    }

    public function getTargetUserId(): TargetUserId
    {
        return $this->targetUserId;
    }

    public function getAttemptSucceeded(): bool
    {
        return $this->attemptSucceeded;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getOs(): string
    {
        return $this->os;
    }

    public function getBrowserName(): string
    {
        return $this->browserName;
    }

    public function getNumberOfDetailsAttachmentTries(): int
    {
        return $this->numberOfDetailsAttachmentTries;
    }

    public function getSuspiciousAuthentication(): bool
    {
        return $this->suspiciousAuthentication;
    }

    private function setIpAddress(IpAddress $ipAddress): void
    {
        if ($ipAddress->getIpAddress() === null) {
            throw new DomainException(
                'Could not set ip address to user authentication attempt.'
                . ' The ip cannot be null.');
        }

        $this->ipAddress = $ipAddress;
    }

    private function setDetailsStatus(AuthenticationDetailsStatus $detailsStatus)
    {
        $this->detailsStatus = $detailsStatus;
    }

    private function setCountryCode($countryCode): void
    {
        if ($countryCode !== null) {
            $countryCode = strtoupper(trim($countryCode));

            if ($countryCode === '') {
                $countryCode = null;
            }
        }

        $this->countryCode = $countryCode;
    }

    private function setState($state): void
    {
        if ($state !== null) {
            if (is_string($state)) {
                $state = ucfirst(trim($state));
            }

            if ($state === '') {
                $state = null;
            }
        }

        $this->state = $state;
    }

    private function setCity($city)
    {
        if ($city !== null) {
            if (is_string($city)) {
                $city = ucfirst(trim($city));
            }

            if ($city === '') {
                $city = null;
            }
        }

        $this->city = $city;
    }

    private function setOs($os)
    {
        if ($os !== null) {
            if (is_string($os)) {
                $os = trim($os);
            }

            if ($os === '') {
                $os = null;
            }
        }

        $this->os = $os;
    }

    private function setBrowserName($browserName)
    {
        if ($browserName !== null) {
            if (is_string($browserName)) {
                $browserName = trim($browserName);
            }

            if ($browserName === '') {
                $browserName = null;
            }
        }

        $this->browserName = $browserName;
    }

    private function setDateOfAttempt(DateTime $dateOfAttempt): void
    {
        $this->dateOfAttempt = $dateOfAttempt;
    }

    private function setTargetUserId(TargetUserId $targetUserId): void
    {
        $this->targetUserId = $targetUserId;
    }

    private function setAttemptSucceeded($attemptSucceeded): void
    {
        if (!is_bool($attemptSucceeded)) {
            throw new DomainException(
                'Could not set attempt succeeded to user authentication attempt.'
                . ' Invalid format.');
        }

        $this->attemptSucceeded = $attemptSucceeded;
    }

    private function setUserAgent($userAgent): void
    {
        if ($userAgent !== null) {
            if (is_string($userAgent)) {
                $userAgent = trim($userAgent);
            } else {
                throw new DomainException(
                    'Invalid user agent. Could not set user agent to'
                    . ' user authentication attempt.');
            }

            if ($userAgent === '') {
                $userAgent = null;
            } else {
                $userAgent = mb_substr($userAgent, 0, 256);
            }
        }

        $this->userAgent = $userAgent;
    }

    private function setReferer($referer): void
    {
        if ($referer !== null) {
            if (is_string($referer)) {
                $referer = trim($referer);
            } else {
                throw new DomainException(
                    'Invalid referer. Could not set referer to'
                    . ' user athentication attempt.');
            }

            if ($referer === '') {
                $referer = null;
            } else {
                $referer = mb_substr($referer, 0, 256);
            }
        }

        $this->referer = $referer;
    }

    private function setNumberOfDetailsAttachmentTries($numberOfDetailsAttachmentTries): void
    {
        if (!is_int($numberOfDetailsAttachmentTries)) {
            throw new DomainException(
                'Could nto set number of details attachement tries. Invalid format.');
        }

        $this->numberOfDetailsAttachmentTries = $numberOfDetailsAttachmentTries;
    }

    public function setSuspiciousAuthentication($suspiciousAuthentication): void
    {
        if (!is_bool($suspiciousAuthentication)) {
            throw new DomainException('Could not set suspicious authentication. Invalid format.');
        }

        $this->suspiciousAuthentication = $suspiciousAuthentication;
    }

    public static function reconstitute(
        UserAuthenticationAttemptId $id,
        IpAddress                   $ipAddress,
        DateTime                    $dateOfAttempt,
        TargetUserId                $targetUserId,
                                    $succeeded,
                                    $userAgent,
                                    $referer,
        AuthenticationDetailsStatus $detailsStatus,
                                    $countryCode,
                                    $state,
                                    $city,
                                    $os,
                                    $browserName,
        $numberOfDetailsAttachmentTries,
        $suspiciousAuthentication
    ): UserAuthenticationAttempt
    {
        $self = new self();
        $self->id = $id;
        $self->ipAddress = $ipAddress;
        $self->detailsStatus = $detailsStatus;
        $self->targetUserId = $targetUserId;
        $self->attemptSucceeded = $succeeded;
        $self->dateOfAttempt = $dateOfAttempt;
        $self->userAgent = $userAgent;
        $self->referer = $referer;
        $self->countryCode = $countryCode;
        $self->state = $state;
        $self->city = $city;
        $self->os = $os;
        $self->browserName = $browserName;
        $self->numberOfDetailsAttachmentTries = $numberOfDetailsAttachmentTries;
        $self->suspiciousAuthentication = $suspiciousAuthentication;
        return $self;
    }
}
