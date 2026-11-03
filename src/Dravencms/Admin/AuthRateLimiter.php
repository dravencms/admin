<?php declare(strict_types=1);

namespace Dravencms\Admin;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Lock\{LockFactory, Store\FlockStore};
use Symfony\Component\RateLimiter\{RateLimiterFactory, Storage\CacheStorage};

final class AuthRateLimiter
{
    private CacheStorage $storage;
    private LockFactory $locks;

    public function __construct(string $directory)
    {
        if (!is_dir($directory) && !mkdir($directory, 0770, true) && !is_dir($directory)) { throw new \RuntimeException('Cannot create authentication limiter directory.'); }
        $this->storage = new CacheStorage(new FilesystemAdapter('dravencms_admin_auth', 0, $directory));
        $this->locks = new LockFactory(new FlockStore($directory));
    }

    public function consume(string $purpose, string $email, string $ip): bool
    {
        if (!in_array($purpose, ['login', 'reset'], true)) { throw new \InvalidArgumentException('Unknown authentication limit.'); }
        $limits = $purpose === 'login' ? [10, 30, '15 minutes'] : [3, 10, '1 hour'];
        $accepted = true;
        foreach (['account' => [strtolower(trim($email)), $limits[0]], 'ip' => [$ip, $limits[1]]] as $kind => [$key, $maximum]) {
            $factory = new RateLimiterFactory(['id' => $purpose . '_' . $kind, 'policy' => 'fixed_window', 'limit' => $maximum, 'interval' => $limits[2]], $this->storage, $this->locks);
            $limit = $factory->create(hash('sha256', $key))->consume();
            $accepted = $limit->isAccepted() && $accepted;
        }
        return $accepted;
    }
}
