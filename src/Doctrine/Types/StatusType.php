<?php

namespace Bnza\JobManagerBundle\Doctrine\Types;

use Bnza\JobManagerBundle\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class StatusType extends Type
{
    public const string STATUS_TYPE = 'job_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'INT';
    }

    public function getName(): string
    {
        return self::STATUS_TYPE;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): Status
    {
        return new Status(is_null($value) ? null : $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        /** @var Status */
        return is_null($value) ? 0 : $value();
    }

    public function getDoctrineTypeMapping(): string
    {
        return self::STATUS_TYPE;
    }

    public function requiresSQLComment(AbstractPlatform $platform): bool
    {
        return true; // Often helpful for custom types
    }
}
