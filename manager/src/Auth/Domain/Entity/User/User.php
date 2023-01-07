<?php

declare(strict_types=1);

namespace App\Auth\Domain\Entity\User;

use App\Auth\Infrastructure\Doctrine\Type\EmailType;
use App\Auth\Infrastructure\Doctrine\Type\IdType;
use App\Auth\Infrastructure\Doctrine\Type\RoleType;
use App\Auth\Infrastructure\Doctrine\Type\StatusType;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'auth_users')]
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

    private function __construct(Id $id, DateTimeImmutable $date, Email $email)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function create(Id $id, DateTimeImmutable $date, Email $email, string $hash): self
    {
        $user = new self($id, $date, $email);
        $user->passwordHash = $hash;
        $user->status = Status::active();
        return $user;
    }

    public static function signUpByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        string $hash,
        Token $token
    ): self {
        $user = new self($id, $date, $email);
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = Status::wait();
        return $user;
    }

    public static function signUpByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Network $network
    ): self {
        $user = new self($id, $date, $email);
        $user->status = Status::active();
        $user->networks->add(new UserNetwork($user, $network));
        return $user;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('User is already confirmed.');
        }

        $this->status = Status::active();
        $this->confirmToken = null;
    }

    public function attachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                throw new DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new UserNetwork($this, $network));
    }

    public function detachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                if ($this->networks->count() === 1) {
                    throw new DomainException('Unable to detach the last identity.');
                }
                $this->networks->removeElement($existing);
                return;
            }
        }
        throw new DomainException('Network is not attached.');
    }

    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requested.');
        }
        $this->passwordResetToken = $token;
    }

    public function resetPassword(string $token, DateTimeImmutable $date, string $hash): void
    {
        if ($this->passwordResetToken === null) {
            throw new DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($token, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if ($this->email->isEqualTo($email)) {
            throw new DomainException('Email is already same.');
        }
        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Changing is already requested.');
        }
        $this->newEmail = $email;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if ($this->newEmail === null || $this->newEmailToken === null) {
            throw new DomainException('Changing is not requested.');
        }
        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new DomainException('Role is already same.');
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
        /** @var Network[] */
        return $this->networks->map(static fn (UserNetwork $network) => $network->getNetwork())->toArray();
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
