<?php

namespace Bnza\JobManagerBundle\Doctrine\Types;

use Bnza\JobManagerBundle\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;

class MicrotimeType extends Type
{
    public const string MICROTIME_TYPE = 'microtime';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'BIGINT';
    }

    public function getName(): string
    {
        return self::MICROTIME_TYPE;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): float|null
    {

        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (float)$value / 1000.0; // Milliseconds to seconds
        }

        throw new InvalidArgumentException("Invalid microtime value: ".var_export($value, true));

    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int|null
    {
        if ($value === null) {
            return null;
        }

        // Convert from PHP float (seconds) to database value (milliseconds - integer)
        if (is_float($value) || is_int($value)) {
            return (int)($value * 1000);
        }

        throw new InvalidArgumentException("Invalid microtime value: ".var_export($value, true));
    }

    public function getDoctrineTypeMapping(): string
    {
        return self::MICROTIME_TYPE;
    }

    public function requiresSQLComment(AbstractPlatform $platform): bool
    {
        return true; // Often helpful for custom types
    }
}
