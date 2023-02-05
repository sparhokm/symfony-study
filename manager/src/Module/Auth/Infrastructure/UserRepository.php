<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure;

use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\Network;
use App\Module\Auth\Domain\Entity\User\User;
use App\Module\Auth\Domain\Exception\User\UserNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class UserRepository
{
    /**
     * @var EntityRepository<User>
     */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken.value' => $token]);
    }

    public function findByPasswordResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['passwordResetToken.value' => $token]);
    }

    public function get(Id $id): User
    {
        if (!$user = $this->repo->find($id->getValue())) {
            throw new UserNotFound();
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $email->getValue()])) {
            throw new UserNotFound();
        }

        return $user;
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNetwork(Network $network): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->innerJoin('t.networks', 'n')
            ->andWhere('n.network.name = :name and n.network.identity = :identity')
            ->setParameter(':name', $network->getName())
            ->setParameter(':identity', $network->getIdentity())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}
