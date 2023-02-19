<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Entity\User;

use App\Common\Infrastructure\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'auth_user_networks')]
#[ORM\UniqueConstraint(columns: ['network_name', 'network_identity'])]
/** @final */
class UserNetwork
{
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::GUID)]
    #[ORM\Id]
    private string $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'networks')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Embedded(class: Network::class)]
    private Network $network;

    public function __construct(User $user, Network $network)
    {
        $this->id = Uuid::getUuid7();
        $this->user = $user;
        $this->network = $network;
    }

    public function getNetwork(): Network
    {
        return $this->network;
    }
}
