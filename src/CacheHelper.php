<?php

namespace Bnza\JobManagerBundle;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class CacheHelper
{
    private const string PREFIX = 'bnza.job_manager';

    public const string KEY_STATUS = 'status';

    public const string KEY_CURRENT_STEP_NUMBER = 'current_step_number';

    public function __construct(
        private readonly CacheItemPoolInterface $cache,
    ) {
    }


    public function set(Uuid $uuid, string|array $propOrArray, mixed $value): array
    {
        $item = $this->cache->getItem($this->getKey($uuid));

        $cached = $item->isHit()
            ? $item->get()
            : [];

        $merged = array_merge(
            $cached,
            (
            is_array($propOrArray)
                ? $propOrArray
                : [$propOrArray => $value]
            )
        );

        $item->set($merged);
        $this->cache->save($item);

        return $merged;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(Uuid $uuid, ?string $prop = null): mixed
    {
        $item = $this->cache->getItem($this->getKey($uuid));
        $cached = $item->get();
        if (is_array($cached) && !is_null($prop)) {
            return array_key_exists($prop, $cached) ? $cached[$prop] : null;
        }

        return $cached;
    }

    private function getKey(Uuid $uuid): string
    {
        return self::PREFIX.".".$uuid;
    }

}
