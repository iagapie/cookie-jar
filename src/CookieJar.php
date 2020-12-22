<?php

declare(strict_types=1);

namespace IA\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

use function array_key_exists;
use function array_values;
use function array_walk_recursive;
use function strtolower;

class CookieJar implements CookieJarInterface
{
    /**
     * @var bool
     */
    protected bool $secure;

    /**
     * @var string
     */
    protected string $sameSite;

    /**
     * @var array<string, array<string, Cookie>>
     */
    protected array $cookies = [];

    /**
     * @param string $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param string|null $sameSite
     */
    public function __construct(
        protected string $path = '/',
        protected ?string $domain = null,
        ?bool $secure = null,
        ?string $sameSite = null
    ) {
        $this->secure = $secure ?? $this->isSecure();
        $this->sameSite = $sameSite ?? Cookie::SAMESITE_LAX;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        $return = [];

        array_walk_recursive(
            $this->cookies,
            function ($a) use (&$return) {
                $return[] = $a;
            }
        );

        return $return;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, ?string $path = null, Cookie|array|null $default = null): Cookie|array|null
    {
        if ($this->has($name, $path)) {
            return null === $path ? array_values($this->cookies[$name]) : $this->cookies[$name][$path];
        }

        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name, ?string $path = null): bool
    {
        $hasName = array_key_exists($name, $this->cookies);

        return null === $path ? $hasName : $hasName && array_key_exists($path, $this->cookies[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function add(...$parameters): void
    {
        if (isset($parameters) && $parameters[0] instanceof Cookie) {
            $cookie = $parameters[0];
        } else {
            $cookie = $this->create(...$parameters);
        }

        $this->cookies[$cookie->getName()][$cookie->getPath()] = $cookie;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name, ?string $path = null): void
    {
        if (null === $path) {
            unset($this->cookies[$name]);

            return;
        }

        unset($this->cookies[$name][$path]);

        if (empty($this->cookies[$name])) {
            unset($this->cookies[$name]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        string $name,
        ?string $value,
        int $expire = 0,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ): Cookie {
        [$path, $domain, $secure, $sameSite] = [
            $path ?: $this->path,
            $domain ?: $this->domain,
            $secure ?? $this->secure,
            $sameSite ?: $this->sameSite,
        ];

        return new Cookie($name, $value, $expire, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * {@inheritDoc}
     */
    public function forever(
        string $name,
        ?string $value,
        ?string $path = null,
        ?string $domain = null,
        ?bool $secure = null,
        bool $httpOnly = true,
        bool $raw = false,
        ?string $sameSite = null
    ): Cookie {
        return $this->create($name, $value, 157680000, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * {@inheritDoc}
     */
    public function forget(string $name, ?string $path = null, ?string $domain = null): Cookie
    {
        return $this->create($name, null, -157680000, $path, $domain);
    }

    /**
     * @return bool
     */
    protected function isSecure(): bool
    {
        return array_key_exists('HTTPS', $_SERVER)
            && !empty($_SERVER['HTTPS'])
            && 'off' !== strtolower($_SERVER['HTTPS']);
    }
}