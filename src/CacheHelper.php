<?php

namespace Bnza\JobManagerBundle;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Uid\Uuid;

class CacheHelper
{
    private const PREFIX = 'bnza.job_manager';

    public const KEY_CURRENT_STEP_NUMBER = 'current_step_number';

    public const KEY_IS_CANCELLED = 'cancelled';

    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public function set(Uuid $uuid, string $prop, mixed $value): array
    {
        $key = self::PREFIX.$uuid;
        $item = $this->cache->getItem($key);

        $cached = $item->isHit() ? $item->get() : [];
        $merged = array_merge($cached, [$prop => $value]);

        $item->set($merged);
        $this->cache->save($item);

        return $merged;
    }

    public function get(Uuid $uuid, ?string $prop = null): mixed
    {
        $key = self::PREFIX.$uuid;
        $item = $this->cache->getItem($key);
        $cached = $item->get();
        if (is_array($cached) && !is_null($prop)) {
            return array_key_exists($prop, $cached) ? $cached[$prop] : null;
        }

        return $cached;
    }

}
