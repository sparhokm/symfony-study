<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Type;

use App\Module\Auth\Domain\Entity\User\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class StatusType extends StringType
{
    public const NAME = 'auth_user_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof Status ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Status
    {
        return !empty($value) ? new Status((string)$value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
