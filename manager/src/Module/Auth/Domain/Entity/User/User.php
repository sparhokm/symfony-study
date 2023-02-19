<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use App\Module\Auth\Domain\Exception\User\ConfirmationNotRequired;
use App\Module\Auth\Domain\Exception\User\EmailAlreadySame;
use App\Module\Auth\Domain\Exception\User\EmailChangeAlreadyRequested;
use App\Module\Auth\Domain\Exception\User\EmailChangeNotRequested;
use App\Module\Auth\Domain\Exception\User\NetworkAlreadyAttached;
use App\Module\Auth\Domain\Exception\User\NetworkNotFound;
use App\Module\Auth\Domain\Exception\User\PasswordEqualOldPassword;
use App\Module\Auth\Domain\Exception\User\PasswordIncorrect;
use App\Module\Auth\Domain\Exception\User\PasswordResetAlreadyRequested;
use App\Module\Auth\Domain\Exception\User\PasswordResetNotRequested;
use App\Module\Auth\Domain\Exception\User\RoleAlreadySame;
use App\Module\Auth\Domain\Exception\User\UnableDetachLastIdentity;
use App\Module\Auth\Domain\Exception\User\UserNotActive;
use App\Module\Auth\Infrastructure\Doctrine\Type\EmailType;
use App\Module\Auth\Infrastructure\Doctrine\Type\IdType;
use App\Module\Auth\Infrastructure\Doctrine\Type\RoleType;
use App\Module\Auth\Infrastructure\Doctrine\Type\StatusType;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'auth_users')]
/** @final */
class User
{
    #[ORM\Column(type: IdType::NAME)]
    #[ORM\Id]
    private Id $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: EmailType::NAME, unique: true)]
    private Email $email;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: StatusType::NAME, length: 16)]
    private Status $status;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $confirmToken = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $passwordResetToken = null;

    #[ORM\Column(type: EmailType::NAME, nullable: true)]
    private ?Email $newEmail = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $newEmailToken = null;

    #[ORM\Column(type: RoleType::NAME, length: 16)]
    private Role $role;

    /**
     * @var Collection<array-key,UserNetwork>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserNetwork::class, cascade: ['all'], orphanRemoval: true)]
    private Collection $networks;

    #[ORM\Version]
    private int $version;

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
        $this->status = $status;
        $this->version = 0;
    }

    public static function joinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $hash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $hash;
        $user->confirmToken = $token;

        return $user;
    }

    public static function joinByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Network $network
    ): self {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->add(new UserNetwork($user, $network));

        return $user;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if ($this->confirmToken === null) {
            throw new ConfirmationNotRequired();
        }
        $this->confirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->confirmToken = null;
    }

    public function attachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                throw new NetworkAlreadyAttached();
            }
        }
        $this->networks->add(new UserNetwork($this, $network));
    }

    public function detachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                if ($this->networks->count() === 1) {
                    throw new UnableDetachLastIdentity();
                }
                $this->networks->removeElement($existing);

                return;
            }
        }

        throw new NetworkNotFound();
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new UserNotActive();
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new PasswordResetAlreadyRequested();
        }
        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if ($this->passwordResetToken === null) {
            throw new PasswordResetNotRequested();
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new UserNotActive();
        }
        if ($this->email->isEqualTo($email)) {
            throw new EmailAlreadySame();
        }
        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) {
            throw new EmailChangeAlreadyRequested();
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if ($this->newEmail === null || $this->newEmailToken === null) {
            throw new EmailChangeNotRequested();
        }
        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->passwordHash === null) {
            throw new PasswordEqualOldPassword();
        }
        if (!$hasher->validate($current, $this->passwordHash)) {
            throw new PasswordIncorrect();
        }
        $this->passwordHash = $hasher->hash($new);
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new RoleAlreadySame();
        }
        $this->role = $role;
    }

    public function isWait(): bool
    {
        return $this->status->isEqualTo(Status::wait());
    }

    public function isActive(): bool
    {
        return $this->status->isEqualTo(Status::active());
    }

    public function isBlocked(): bool
    {
        return $this->status->isEqualTo(Status::blocked());
    }

    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->map(static fn (UserNetwork $network) => $network->getNetwork())->toArray();
    }

    public function getConfirmToken(): ?Token
    {
        return $this->confirmToken;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    #[ORM\PostLoad]
    public function checkEmbeds(): void
    {
        if ($this->confirmToken && $this->confirmToken->isEmpty()) {
            $this->confirmToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}
