<?php

declare(strict_types=1);

namespace App\Module\Auth\Fixture;

use App\Common\Infrastructure\Uuid;
use App\Module\Auth\Domain\Entity\User\Email;
use App\Module\Auth\Domain\Entity\User\Id;
use App\Module\Auth\Domain\Entity\User\Token;
use App\Module\Auth\Domain\Entity\User\User;
use App\Module\Auth\Infrastructure\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/** @psalm-suppress PropertyNotSetInConstructor */
final class UserFixture extends Fixture
{
    private const PASSWORD = 'password';

    public function __construct(
        private readonly PasswordHasher $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = User::joinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new DateTimeImmutable('-30 days'),
            new Email('user@app.test'),
            $this->passwordHasher->hash(self::PASSWORD),
            new Token($value = Uuid::getUuid7(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date);

        $manager->persist($user);

        $manager->flush();
    }
}
